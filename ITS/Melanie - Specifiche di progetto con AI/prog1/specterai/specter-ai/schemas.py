from typing import Annotated, Literal
from pydantic import BaseModel, BeforeValidator, model_validator, field_validator


def _normalize_excerpt(v: object) -> list[str]:
    if isinstance(v, str):
        return [v] if v else []
    return v  # type: ignore[return-value]


RawExcerpt = Annotated[list[str], BeforeValidator(_normalize_excerpt)]


class CategoryResult(BaseModel):
    present: bool
    raw_excerpt: RawExcerpt
    plain_language: str
    risk_level: Literal["low", "medium", "high"]
    question_to_ask: str

    @model_validator(mode="after")
    def validate_excerpts(self) -> "CategoryResult":
        if not self.present:
            return self
        if not self.raw_excerpt:
            raise ValueError("raw_excerpt non può essere vuoto quando present=true")
        for span in self.raw_excerpt:
            if len(span) < 20:
                raise ValueError(f"Ogni span di raw_excerpt deve essere ≥20 caratteri: '{span}'")
            if "[...]" in span or "..." in span:
                raise ValueError(f"raw_excerpt non può contenere ellissi: '{span}'")
        return self


class ContractAnalysis(BaseModel):
    language_detected: Literal["italian", "english"]
    categories: dict[str, CategoryResult]
    top_3_risks: list[str]

    @field_validator("top_3_risks")
    @classmethod
    def validate_top3(cls, v: list[str]) -> list[str]:
        if len(v) != 3:
            raise ValueError(f"top_3_risks deve contenere esattamente 3 elementi, trovati {len(v)}")
        return v

    @model_validator(mode="after")
    def validate_categories(self) -> "ContractAnalysis":
        expected = {
            "payment_terms", "auto_renewal", "penalties",
            "liability_limitation", "termination", "governing_law", "intellectual_property"
        }
        missing = expected - set(self.categories.keys())
        if missing:
            raise ValueError(f"Categorie mancanti nel JSON: {missing}")
        return self
