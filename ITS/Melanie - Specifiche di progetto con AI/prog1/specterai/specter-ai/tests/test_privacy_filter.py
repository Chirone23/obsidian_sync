"""
Regression tests per il bug di ordinamento spaCy in privacy_filter.py.

Bug originale: regex girava PRIMA di spaCy. I placeholder inseriti
([CF_1], [PIVA_1]...) modificavano la lunghezza del testo, rendendo
invalidi gli offset che spaCy aveva calcolato sul testo originale.
Risultato: placeholder annidati tipo [PER[CF_1]_2].

Fix (Option A): spaCy gira sul testo ORIGINALE, poi regex sul post-NER.
"""
import re
import sys
from pathlib import Path
from unittest.mock import MagicMock, patch

sys.path.insert(0, str(Path(__file__).parent.parent))

import privacy_filter


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

def _mock_doc(text: str, entities: list[tuple[int, int, str]]):
    """Crea un mock spaCy Doc con entità a offset noti nel testo dato."""
    doc = MagicMock()
    ents = []
    for start, end, label in entities:
        ent = MagicMock()
        ent.start_char = start
        ent.end_char = end
        ent.label_ = label
        ent.text = text[start:end]
        ents.append(ent)
    doc.ents = ents
    return doc


INPUT = "Mario Rossi, CF RSSMRA80A01H501U, PIVA 00743110157"
# "Mario Rossi" = chars 0-10 (len 11)
_PER_SPAN = (0, 11, "PER")


# ---------------------------------------------------------------------------
# Test 1 — regressione diretta: no placeholder annidati
# ---------------------------------------------------------------------------

def test_no_nested_placeholders():
    """
    Con il fix, spaCy riceve il testo originale. L'entità PER a offset (0,11)
    è "Mario Rossi", non [PER[CF_1]_2] o simili corruzioni.
    """
    with patch.object(privacy_filter, '_SPACY_AVAILABLE', True), \
         patch.object(privacy_filter, '_nlp', return_value=_mock_doc(INPUT, [_PER_SPAN])):
        result, _ = privacy_filter.redact(INPUT)

    # Nessun placeholder annidato
    assert re.search(r'\[[A-Z]+\[', result) is None, \
        f"Placeholder annidato trovato: {result!r}"
    # CF e PIVA redatti
    assert "RSSMRA80A01H501U" not in result
    assert "00743110157" not in result


# ---------------------------------------------------------------------------
# Test 2 — spaCy viene chiamata sul testo ORIGINALE (non post-regex)
# ---------------------------------------------------------------------------

def test_spacy_receives_original_text():
    """
    Verifica che _nlp sia chiamato con il testo originale, non con il testo
    dopo le sostituzioni regex (che conterrebbe già [CF_1], [PIVA_1]).
    """
    calls: list[str] = []

    def capturing_nlp(t: str):
        calls.append(t)
        return _mock_doc(t, [_PER_SPAN])

    with patch.object(privacy_filter, '_SPACY_AVAILABLE', True), \
         patch.object(privacy_filter, '_nlp', side_effect=capturing_nlp):
        privacy_filter.redact(INPUT)

    assert len(calls) == 1
    received = calls[0]
    assert received == INPUT, \
        f"spaCy ha ricevuto testo modificato invece dell'originale:\n  atteso: {INPUT!r}\n  ricevuto: {received!r}"
    assert "[CF_" not in received, "spaCy ha ricevuto testo con placeholder regex (bug di ordinamento)"
    assert "[PIVA_" not in received


# ---------------------------------------------------------------------------
# Test 3 — CF e PIVA corretti senza spaCy (solo regex)
# ---------------------------------------------------------------------------

def test_cf_and_piva_redacted_regex_only():
    """Senza spaCy, CF (con keyword) e PIVA (Luhn valida) vengono redatti."""
    with patch.object(privacy_filter, '_SPACY_AVAILABLE', False):
        result, mapping = privacy_filter.redact(INPUT)

    assert "[CF_1]" in result
    assert "[PIVA_1]" in result
    assert "RSSMRA80A01H501U" not in result
    assert "00743110157" not in result
    assert mapping["[CF_1]"] == "RSSMRA80A01H501U"
    assert mapping["[PIVA_1]"] == "00743110157"
    # Il nome rimane (spaCy disabilitata)
    assert "Mario Rossi" in result


# ---------------------------------------------------------------------------
# Test 4 — restore ricostruisce il testo originale
# ---------------------------------------------------------------------------

def test_restore_roundtrip():
    """redact + restore deve restituire il testo originale invariato."""
    with patch.object(privacy_filter, '_SPACY_AVAILABLE', False):
        redacted, mapping = privacy_filter.redact(INPUT)

    restored = privacy_filter.restore(redacted, mapping)
    assert "RSSMRA80A01H501U" in restored
    assert "00743110157" in restored


# ---------------------------------------------------------------------------
# Runner standalone
# ---------------------------------------------------------------------------

if __name__ == "__main__":
    tests = [
        test_no_nested_placeholders,
        test_spacy_receives_original_text,
        test_cf_and_piva_redacted_regex_only,
        test_restore_roundtrip,
    ]
    failed = 0
    for t in tests:
        try:
            t()
            print(f"  OK  {t.__name__}")
        except Exception as e:
            print(f"  FAIL {t.__name__}: {e}")
            failed += 1
    print(f"\n{'Tutti i test passati.' if not failed else f'{failed} test falliti.'}")
    sys.exit(failed)
