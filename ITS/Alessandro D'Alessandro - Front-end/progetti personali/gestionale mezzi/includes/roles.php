<?php

function gm_register_roles() {
    add_role( 'gm_volontario', 'Volontario', [
        'read' => true,
        'gm_create_foglio' => true,
        'gm_edit_own_foglio' => true,
    ] );

    add_role( 'gm_direttivo', 'Direttivo', [
        'read' => true,
        'gm_create_foglio' => true,
        'gm_edit_own_foglio' => true,
        'gm_read_all' => true,
        'gm_edit_all' => true,
        'gm_manage_users' => true,
        'gm_manage_veicoli' => true,
    ] );

    add_role( 'gm_amministrazione', 'Amministrazione', [
        'read' => true,
        'gm_create_foglio' => true,
        'gm_edit_own_foglio' => true,
        'gm_read_all' => true,
        'gm_edit_all' => true,
        'gm_manage_users' => true,
        'gm_manage_veicoli' => true,
        'gm_delete_any' => true,
        'gm_view_log' => true,
    ] );
}

function gm_remove_roles() {
    remove_role( 'gm_volontario' );
    remove_role( 'gm_direttivo' );
    remove_role( 'gm_amministrazione' );
}
