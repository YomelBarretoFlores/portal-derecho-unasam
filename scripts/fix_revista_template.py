"""Crea una copia normativa de la plantilla editorial sin modificar el original."""

from pathlib import Path
import sys

from docx import Document
from docx.enum.section import WD_SECTION
from docx.oxml.ns import qn
from docx.shared import Cm, Mm, Pt


def set_font(run, size: int) -> None:
    run.font.name = "Garamond"
    run._element.get_or_add_rPr().rFonts.set(qn("w:ascii"), "Garamond")
    run._element.get_or_add_rPr().rFonts.set(qn("w:hAnsi"), "Garamond")
    run._element.get_or_add_rPr().rFonts.set(qn("w:eastAsia"), "Garamond")
    run.font.size = Pt(size)


def main(source: str, destination: str) -> None:
    source_path = Path(source)
    destination_path = Path(destination)
    document = Document(source_path)

    for section in document.sections:
        section.page_width = Mm(210)
        section.page_height = Mm(297)
        section.left_margin = Cm(3)
        section.top_margin = Cm(2.5)
        section.right_margin = Cm(2.5)
        section.bottom_margin = Cm(2.5)
        section.start_type = WD_SECTION.NEW_PAGE if section is not document.sections[0] else section.start_type

    body_styles = {
        "Normal", "Placeholder", "Author Name", "Affiliation",
        "Article Body", "References", "Correspondence",
    }
    title_styles = {"Heading 1", "Heading 2", "Heading 3", "Article Title ES", "Article Title EN"}

    for style_name in body_styles | title_styles:
        try:
            style = document.styles[style_name]
        except KeyError:
            continue
        style.font.name = "Garamond"
        style._element.get_or_add_rPr().rFonts.set(qn("w:ascii"), "Garamond")
        style._element.get_or_add_rPr().rFonts.set(qn("w:hAnsi"), "Garamond")
        style.font.size = Pt(13 if style_name in title_styles else 12)
        style.paragraph_format.line_spacing = 1.5

    manuscript_started = False
    for paragraph in document.paragraphs:
        if paragraph.text.strip() == "INICIO DEL MANUSCRITO":
            manuscript_started = True
            continue

        if "configuración base Garamond de 11 puntos" in paragraph.text:
            replacement = (
                "Conforme a las Normas de Publicación aprobadas, el manuscrito debe presentarse "
                "en papel A4, con Garamond de 12 puntos para el cuerpo, títulos Garamond de 13 "
                "puntos, texto justificado, interlineado 1.5, margen izquierdo de 3 cm y márgenes "
                "superior, inferior y derecho de 2.5 cm."
            )
            paragraph.text = replacement
            for run in paragraph.runs:
                set_font(run, 10)

        if not manuscript_started:
            continue

        is_title = paragraph.style.name in title_styles
        is_instruction = paragraph.style.name in {"Notice", "Instruction Bullet"}
        if not is_instruction:
            paragraph.paragraph_format.line_spacing = 1.5
            for run in paragraph.runs:
                set_font(run, 13 if is_title else 12)

    destination_path.parent.mkdir(parents=True, exist_ok=True)
    document.save(destination_path)


if __name__ == "__main__":
    if len(sys.argv) != 3:
        raise SystemExit("Uso: fix_revista_template.py ORIGEN.docx DESTINO.docx")
    main(sys.argv[1], sys.argv[2])
