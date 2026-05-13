// MD -> DOCX converter using markdown-it + docx
// Usage: node md2docx.js <input.md> <output.docx>

const fs = require('fs');
const path = require('path');
const MarkdownIt = require('markdown-it');
const {
  Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType,
  Table, TableRow, TableCell, WidthType, BorderStyle, ShadingType,
} = require('docx');

const md = new MarkdownIt({ html: false, linkify: true, breaks: false });

const [, , inPath, outPath] = process.argv;
if (!inPath || !outPath) {
  console.error('Usage: node md2docx.js <input.md> <output.docx>');
  process.exit(1);
}

const src = fs.readFileSync(inPath, 'utf8');
const tokens = md.parse(src, {});

const HEADING_MAP = {
  1: HeadingLevel.HEADING_1,
  2: HeadingLevel.HEADING_2,
  3: HeadingLevel.HEADING_3,
  4: HeadingLevel.HEADING_4,
  5: HeadingLevel.HEADING_5,
  6: HeadingLevel.HEADING_6,
};

// Build TextRun array from an "inline" token's children
function inlineRuns(inlineToken, baseStyle = {}) {
  if (!inlineToken || !inlineToken.children) return [new TextRun({ text: '', ...baseStyle })];
  const runs = [];
  const stack = { bold: false, italic: false, code: false, strike: false, link: null };
  for (const c of inlineToken.children) {
    switch (c.type) {
      case 'text':
        if (c.content) runs.push(new TextRun({
          text: c.content,
          bold: stack.bold || baseStyle.bold,
          italics: stack.italic || baseStyle.italics,
          strike: stack.strike,
          font: stack.code ? 'Consolas' : baseStyle.font,
          color: stack.link ? '2A6DC0' : baseStyle.color,
          underline: stack.link ? {} : undefined,
          size: baseStyle.size,
        }));
        break;
      case 'strong_open': stack.bold = true; break;
      case 'strong_close': stack.bold = false; break;
      case 'em_open': stack.italic = true; break;
      case 'em_close': stack.italic = false; break;
      case 's_open': stack.strike = true; break;
      case 's_close': stack.strike = false; break;
      case 'code_inline':
        runs.push(new TextRun({
          text: c.content,
          font: 'Consolas',
          shading: { type: ShadingType.CLEAR, fill: 'F2F2F2', color: 'auto' },
          size: baseStyle.size,
        }));
        break;
      case 'link_open':
        stack.link = c.attrGet('href');
        break;
      case 'link_close':
        stack.link = null;
        break;
      case 'softbreak':
      case 'hardbreak':
        runs.push(new TextRun({ text: '', break: 1 }));
        break;
      case 'image':
        runs.push(new TextRun({ text: `[img: ${c.content || c.attrGet('alt') || ''}]`, italics: true, color: '888888' }));
        break;
      default:
        if (c.content) runs.push(new TextRun({ text: c.content, ...baseStyle }));
    }
  }
  return runs.length ? runs : [new TextRun({ text: '', ...baseStyle })];
}

// Plain text extraction (for table cells with inline tokens stored as raw)
function inlineRunsFromText(text, baseStyle = {}) {
  // Re-parse a fragment as inline to preserve **bold** etc.
  const parsed = md.parseInline(text, {})[0];
  return inlineRuns(parsed, baseStyle);
}

const blocks = [];
let i = 0;
while (i < tokens.length) {
  const t = tokens[i];

  if (t.type === 'heading_open') {
    const level = parseInt(t.tag.slice(1), 10);
    const inline = tokens[i + 1];
    blocks.push(new Paragraph({
      heading: HEADING_MAP[level] || HeadingLevel.HEADING_2,
      children: inlineRuns(inline),
      spacing: { before: 240, after: 120 },
    }));
    i += 3; // open, inline, close
    continue;
  }

  if (t.type === 'paragraph_open') {
    const inline = tokens[i + 1];
    blocks.push(new Paragraph({
      children: inlineRuns(inline),
      spacing: { after: 120 },
    }));
    i += 3;
    continue;
  }

  if (t.type === 'bullet_list_open' || t.type === 'ordered_list_open') {
    const ordered = t.type === 'ordered_list_open';
    const close = ordered ? 'ordered_list_close' : 'bullet_list_close';
    let j = i + 1;
    let n = 1;
    while (j < tokens.length && tokens[j].type !== close) {
      if (tokens[j].type === 'list_item_open') {
        // find inline inside item
        let k = j + 1;
        while (k < tokens.length && tokens[k].type !== 'list_item_close') {
          if (tokens[k].type === 'paragraph_open' && tokens[k + 1] && tokens[k + 1].type === 'inline') {
            blocks.push(new Paragraph({
              children: inlineRuns(tokens[k + 1]),
              bullet: ordered ? undefined : { level: 0 },
              numbering: ordered ? { reference: 'num', level: 0 } : undefined,
              indent: { left: 360 },
              spacing: { after: 60 },
              ...(ordered ? {} : {}),
            }));
            k += 3;
          } else {
            k++;
          }
        }
        n++;
        j = k;
      }
      j++;
    }
    i = j + 1;
    continue;
  }

  if (t.type === 'fence' || t.type === 'code_block') {
    const lines = (t.content || '').replace(/\n$/, '').split('\n');
    for (const line of lines) {
      blocks.push(new Paragraph({
        children: [new TextRun({ text: line || ' ', font: 'Consolas', size: 18 })],
        shading: { type: ShadingType.CLEAR, fill: 'F5F5F5', color: 'auto' },
        spacing: { after: 0 },
      }));
    }
    blocks.push(new Paragraph({ children: [new TextRun('')], spacing: { after: 120 } }));
    i++;
    continue;
  }

  if (t.type === 'blockquote_open') {
    let j = i + 1;
    while (j < tokens.length && tokens[j].type !== 'blockquote_close') {
      if (tokens[j].type === 'inline') {
        blocks.push(new Paragraph({
          children: inlineRuns(tokens[j], { italics: true, color: '555555' }),
          indent: { left: 360 },
          border: { left: { style: BorderStyle.SINGLE, size: 12, color: 'C75550', space: 8 } },
          spacing: { after: 120 },
        }));
      }
      j++;
    }
    i = j + 1;
    continue;
  }

  if (t.type === 'hr') {
    blocks.push(new Paragraph({
      children: [new TextRun('')],
      border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: 'CCCCCC', space: 1 } },
      spacing: { before: 120, after: 240 },
    }));
    i++;
    continue;
  }

  if (t.type === 'table_open') {
    // collect rows
    const rows = [];
    let j = i + 1;
    let currentRow = null;
    let isHeader = false;
    while (j < tokens.length && tokens[j].type !== 'table_close') {
      const tt = tokens[j];
      if (tt.type === 'thead_open') { isHeader = true; }
      else if (tt.type === 'thead_close') { isHeader = false; }
      else if (tt.type === 'tr_open') { currentRow = { header: isHeader, cells: [] }; }
      else if (tt.type === 'tr_close') { rows.push(currentRow); currentRow = null; }
      else if ((tt.type === 'th_open' || tt.type === 'td_open') && tokens[j + 1] && tokens[j + 1].type === 'inline') {
        currentRow.cells.push({ header: tt.type === 'th_open', inline: tokens[j + 1] });
      }
      j++;
    }
    const docxRows = rows.map(r => new TableRow({
      tableHeader: r.header,
      children: r.cells.map(c => new TableCell({
        children: [new Paragraph({
          children: inlineRuns(c.inline, c.header ? { bold: true } : {}),
        })],
        shading: c.header ? { type: ShadingType.CLEAR, fill: 'F0EBE0', color: 'auto' } : undefined,
        margins: { top: 80, bottom: 80, left: 100, right: 100 },
      })),
    }));
    blocks.push(new Table({
      rows: docxRows,
      width: { size: 100, type: WidthType.PERCENTAGE },
    }));
    blocks.push(new Paragraph({ children: [new TextRun('')], spacing: { after: 120 } }));
    i = j + 1;
    continue;
  }

  i++;
}

const doc = new Document({
  creator: 'Bibliò',
  title: path.basename(inPath, '.md'),
  numbering: {
    config: [{
      reference: 'num',
      levels: [{ level: 0, format: 'decimal', text: '%1.', alignment: AlignmentType.START, style: { paragraph: { indent: { left: 360, hanging: 260 } } } }],
    }],
  },
  styles: {
    default: {
      document: { run: { font: 'Calibri', size: 22 } },
      heading1: { run: { font: 'Georgia', size: 40, bold: true, color: '2A2A2A' }, paragraph: { spacing: { before: 360, after: 180 } } },
      heading2: { run: { font: 'Georgia', size: 32, bold: true, color: 'C75550' }, paragraph: { spacing: { before: 320, after: 160 } } },
      heading3: { run: { font: 'Georgia', size: 26, bold: true, color: '3A4A5C' }, paragraph: { spacing: { before: 240, after: 120 } } },
      heading4: { run: { font: 'Calibri', size: 22, bold: true, color: '2A2A2A' }, paragraph: { spacing: { before: 200, after: 100 } } },
    },
  },
  sections: [{
    properties: { page: { margin: { top: 1000, right: 1000, bottom: 1000, left: 1000 } } },
    children: blocks,
  }],
});

Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync(outPath, buf);
  console.log('OK ->', outPath, '(' + buf.length + ' bytes)');
}).catch(err => {
  console.error('ERR', err);
  process.exit(1);
});
