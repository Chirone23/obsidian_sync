<?php
/**
 * Bibliò — Reader Ingest: admin upload .epub → estrae capitoli → cache statica.
 *
 * Flusso:
 *   1. Admin carica .epub dalla meta box su post/product (book_id)
 *   2. biblio_ingest_epub() apre lo zip, legge OPF spine, estrae XHTML
 *   3. Ogni capitolo viene sanitizzato con wp_kses_post() una volta sola
 *   4. HTML salvato in books-protected/cache/{book_id}/{n}.html
 *   5. manifest.json salvato in cache/{book_id}/manifest.json
 *
 * Struttura su disco:
 *   wp-content/themes/biblio-theme/books-protected/
 *     .htaccess           (Deny from all — servita solo via PHP)
 *     originals/          (.epub originali, opzionali post-ingest)
 *     cache/
 *       {book_id}/
 *         manifest.json
 *         0.html … N.html
 *         cover.jpg       (se estratta)
 */
if (!defined('ABSPATH')) exit;
if (!current_user_can('manage_options')) return; // file caricato solo in contesto admin

/* ------------------------------------------------------------------ */
/* Percorso base cache                                                  */
/* ------------------------------------------------------------------ */

function biblio_ingest_cache_dir(): string {
    return get_template_directory() . '/books-protected/cache';
}

function biblio_ingest_originals_dir(): string {
    return get_template_directory() . '/books-protected/originals';
}

/**
 * Assicura che la directory books-protected esista con .htaccess protettivo.
 */
function biblio_ingest_ensure_protected_dir(): bool {
    $base     = get_template_directory() . '/books-protected';
    $htaccess = $base . '/.htaccess';

    if (!is_dir($base) && !wp_mkdir_p($base)) return false;
    if (!is_dir(biblio_ingest_cache_dir())) wp_mkdir_p(biblio_ingest_cache_dir());
    if (!is_dir(biblio_ingest_originals_dir())) wp_mkdir_p(biblio_ingest_originals_dir());

    if (!file_exists($htaccess)) {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        file_put_contents($htaccess, "Order deny,allow\nDeny from all\n");
    }
    return true;
}

/* ------------------------------------------------------------------ */
/* Funzione principale di ingest                                       */
/* ------------------------------------------------------------------ */

/**
 * Estrae un .epub e scrive la cache statica per book_id.
 *
 * @param string $epub_path   Percorso assoluto al file .epub.
 * @param int    $book_id     ID del post (product o book) associato.
 * @param bool   $strip_imgs  Se true, rimuove img non-cover (riduce inode su IF).
 * @return array{ok:bool, chapters:int, error?:string}
 */
function biblio_ingest_epub(string $epub_path, int $book_id, bool $strip_imgs = true): array {
    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'chapters' => 0, 'error' => 'ZipArchive non disponibile.'];
    }

    if (!biblio_ingest_ensure_protected_dir()) {
        return ['ok' => false, 'chapters' => 0, 'error' => 'Impossibile creare directory cache.'];
    }

    $zip = new ZipArchive();
    if ($zip->open($epub_path) !== true) {
        return ['ok' => false, 'chapters' => 0, 'error' => 'Impossibile aprire il file .epub.'];
    }

    /* ── Leggi container.xml per trovare l'OPF ──────────────────── */
    $container_xml = $zip->getFromName('META-INF/container.xml');
    if (!$container_xml) {
        $zip->close();
        return ['ok' => false, 'chapters' => 0, 'error' => 'container.xml mancante.'];
    }

    libxml_use_internal_errors(true);
    $container = simplexml_load_string($container_xml);
    if (!$container) {
        $zip->close();
        return ['ok' => false, 'chapters' => 0, 'error' => 'container.xml non parsabile.'];
    }

    $ns      = $container->getNamespaces(true);
    $ns_uri  = array_shift($ns) ?: 'urn:oasis:names:tc:opendocument:xmlns:container';
    $rootfile = (string) $container->rootfiles->rootfile['full-path'] ?? '';

    if (!$rootfile) {
        $zip->close();
        return ['ok' => false, 'chapters' => 0, 'error' => 'OPF path non trovato in container.xml.'];
    }

    /* ── Leggi OPF (package document) ───────────────────────────── */
    $opf_content = $zip->getFromName($rootfile);
    if (!$opf_content) {
        $zip->close();
        return ['ok' => false, 'chapters' => 0, 'error' => "OPF non trovato: {$rootfile}."];
    }

    $opf = simplexml_load_string($opf_content);
    if (!$opf) {
        $zip->close();
        return ['ok' => false, 'chapters' => 0, 'error' => 'OPF non parsabile.'];
    }

    $opf_dir = dirname($rootfile);
    if ($opf_dir === '.') $opf_dir = '';

    /* ── Mappa manifest: id → href ───────────────────────────────── */
    $manifest = [];
    foreach ($opf->manifest->item ?? [] as $item) {
        $id   = (string) $item['id'];
        $href = (string) $item['href'];
        $mt   = (string) $item['media-type'];
        $manifest[$id] = ['href' => $href, 'media-type' => $mt];
    }

    /* ── Cover: cerca cover-image nel manifest ───────────────────── */
    $cover_saved = false;
    foreach ($opf->manifest->item ?? [] as $item) {
        $props = (string) $item['properties'];
        if ($props === 'cover-image' || (string) $item['id'] === 'cover-image') {
            $cover_path = $opf_dir ? $opf_dir . '/' . (string) $item['href'] : (string) $item['href'];
            $cover_data = $zip->getFromName($cover_path);
            if ($cover_data) {
                $cover_dir = biblio_ingest_cache_dir() . '/' . $book_id;
                if (!is_dir($cover_dir)) wp_mkdir_p($cover_dir);
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
                file_put_contents($cover_dir . '/cover.jpg', $cover_data);
                $cover_saved = true;
            }
            break;
        }
    }

    /* ── Spine: ordine di lettura ────────────────────────────────── */
    $spine_items = [];
    foreach ($opf->spine->itemref ?? [] as $itemref) {
        $idref = (string) $itemref['idref'];
        if (!empty($manifest[$idref]) && str_contains($manifest[$idref]['media-type'], 'html')) {
            $spine_items[] = $manifest[$idref]['href'];
        }
    }

    if (empty($spine_items)) {
        $zip->close();
        return ['ok' => false, 'chapters' => 0, 'error' => 'Spine vuota: nessun capitolo trovato.'];
    }

    /* ── Estrai e sanitizza ogni capitolo ───────────────────────── */
    $book_cache_dir = biblio_ingest_cache_dir() . '/' . $book_id;
    if (!is_dir($book_cache_dir)) wp_mkdir_p($book_cache_dir);

    $toc       = [];
    $chapter_n = 0;

    foreach ($spine_items as $href) {
        $zip_path = $opf_dir ? $opf_dir . '/' . $href : $href;
        $raw      = $zip->getFromName($zip_path);
        if ($raw === false) continue;

        /* Estrai solo il <body> se presente */
        $html = biblio_ingest_extract_body($raw);

        /* Strip immagini non-cover (obbligatorio su IF per risparmiare inode) */
        if ($strip_imgs) {
            $html = preg_replace('/<img[^>]*>/i', '', $html);
        }

        /* Sanitize una volta sola — mai più a runtime */
        $html = wp_kses_post($html);

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        file_put_contents($book_cache_dir . '/' . $chapter_n . '.html', $html);

        /* Titolo capitolo: cerca <h1>/<h2> o usa fallback */
        $title = biblio_ingest_chapter_title($html, $chapter_n);
        $toc[] = ['n' => $chapter_n, 'title' => $title, 'href' => $chapter_n . '.html'];

        $chapter_n++;
    }

    $zip->close();

    if ($chapter_n === 0) {
        return ['ok' => false, 'chapters' => 0, 'error' => 'Nessun capitolo estratto.'];
    }

    /* ── Scrivi manifest.json ────────────────────────────────────── */
    $manifest_data = [
        'book_id'       => $book_id,
        'chapter_count' => $chapter_n,
        'cover'         => $cover_saved ? 'cover.jpg' : null,
        'toc'           => $toc,
        'ingested_at'   => gmdate('c'),
    ];
    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
    file_put_contents($book_cache_dir . '/manifest.json', wp_json_encode($manifest_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    return ['ok' => true, 'chapters' => $chapter_n];
}

/* ------------------------------------------------------------------ */
/* Helper interni                                                      */
/* ------------------------------------------------------------------ */

/**
 * Estrae il contenuto del <body> da un documento XHTML/HTML.
 */
function biblio_ingest_extract_body(string $html): string {
    if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $m)) {
        return $m[1];
    }
    return $html;
}

/**
 * Cerca un titolo leggibile nel HTML del capitolo.
 */
function biblio_ingest_chapter_title(string $html, int $fallback_n): string {
    foreach (['h1', 'h2', 'h3'] as $tag) {
        if (preg_match("/<{$tag}[^>]*>(.*?)<\/{$tag}>/is", $html, $m)) {
            $t = trim(wp_strip_all_tags($m[1]));
            if ($t) return $t;
        }
    }
    return 'Capitolo ' . ($fallback_n + 1);
}

/* ------------------------------------------------------------------ */
/* Meta box admin "ePub Reader"                                        */
/* ------------------------------------------------------------------ */

add_action('add_meta_boxes', 'biblio_ingest_meta_box');

function biblio_ingest_meta_box(): void {
    $screens = ['product', 'book'];
    foreach ($screens as $screen) {
        add_meta_box(
            'biblio_epub_ingest',
            'Bibliò Reader — Upload ePub',
            'biblio_ingest_meta_box_html',
            $screen,
            'normal',
            'default'
        );
    }
}

function biblio_ingest_meta_box_html(WP_Post $post): void {
    wp_nonce_field('biblio_ingest_upload', 'biblio_ingest_nonce');
    $book_id    = $post->ID;
    $cache_dir  = biblio_ingest_cache_dir() . '/' . $book_id;
    $manifest   = is_file($cache_dir . '/manifest.json')
        ? json_decode(file_get_contents($cache_dir . '/manifest.json'), true)
        : null;
    ?>
    <div style="padding:8px 0;">
        <?php if ($manifest): ?>
            <p style="color:#1a7a1a;font-weight:600;">
                Cache attiva: <?php echo (int) $manifest['chapter_count']; ?> capitoli.
                Ingested: <?php echo esc_html($manifest['ingested_at'] ?? '—'); ?>
            </p>
            <p style="font-size:12px;color:#555;">
                Per sostituire il libro, carica un nuovo .epub e salva.
            </p>
        <?php else: ?>
            <p style="color:#888;">Nessuna cache presente per questo libro.</p>
        <?php endif; ?>

        <label for="biblio_epub_file" style="font-weight:600;display:block;margin:8px 0 4px;">
            Carica .epub (max 8 MB):
        </label>
        <input type="file" name="biblio_epub_file" id="biblio_epub_file" accept=".epub">

        <label style="display:flex;align-items:center;gap:6px;margin-top:8px;font-size:13px;">
            <input type="checkbox" name="biblio_strip_imgs" value="1" checked>
            Rimuovi immagini non-cover (consigliato su InfinityFree)
        </label>
    </div>
    <?php
}

add_action('save_post', 'biblio_ingest_handle_upload');

function biblio_ingest_handle_upload(int $post_id): void {
    if (!isset($_POST['biblio_ingest_nonce'])) return;
    if (!wp_verify_nonce($_POST['biblio_ingest_nonce'], 'biblio_ingest_upload')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (empty($_FILES['biblio_epub_file']['tmp_name'])) return;

    $file = $_FILES['biblio_epub_file'];

    /* Validazione: solo .epub, max 8 MB */
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'epub') {
        add_action('admin_notices', fn() => print('<div class="notice notice-error"><p>Bibliò Reader: il file deve essere .epub.</p></div>'));
        return;
    }
    if ($file['size'] > 8 * 1024 * 1024) {
        add_action('admin_notices', fn() => print('<div class="notice notice-error"><p>Bibliò Reader: file troppo grande (max 8 MB).</p></div>'));
        return;
    }

    $strip_imgs = !empty($_POST['biblio_strip_imgs']);

    /* Sposta in originals/ prima dell'ingest */
    biblio_ingest_ensure_protected_dir();
    $dest = biblio_ingest_originals_dir() . '/' . $post_id . '.epub';
    move_uploaded_file($file['tmp_name'], $dest);

    $result = biblio_ingest_epub($dest, $post_id, $strip_imgs);

    if ($result['ok']) {
        update_post_meta($post_id, '_biblio_epub_chapters', $result['chapters']);
        add_action('admin_notices', function () use ($result) {
            printf('<div class="notice notice-success"><p>Bibliò Reader: ingest completato — %d capitoli estratti.</p></div>', $result['chapters']);
        });
    } else {
        add_action('admin_notices', function () use ($result) {
            printf('<div class="notice notice-error"><p>Bibliò Reader errore: %s</p></div>', esc_html($result['error'] ?? 'Errore sconosciuto.'));
        });
    }
}
