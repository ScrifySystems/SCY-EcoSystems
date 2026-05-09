<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCY Core | <?= htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        base:   '#09090b',
                        panel:  '#0f0f12',
                        hover:  '#1a1a1f',
                        border: '#27272a',
                        accent: '#00d9ff',
                        dim:    '#71717a',
                        muted:  '#3f3f46',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- CodeMirror 5 for syntax highlighting -->
    <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
    <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/shell/shell.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/sql/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/yaml/yaml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/selection/active-line.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/search/searchcursor.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/comment/comment.min.js"></script>

    <style>
        /* Make CodeMirror fill its parent */
        .CodeMirror {
            height: 100%;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.65;
            background: #09090b !important;
            border: none;
            color: #fff;
        }
        .CodeMirror-scroll { height: 100%; }
        .CodeMirror-gutters { background: #09090b !important; border-right: 1px solid #27272a !important; }
        .CodeMirror-linenumber { color: #3f3f46 !important; padding: 0 10px; }
        .CodeMirror-activeline-background { background: rgba(255,255,255,.025) !important; }
        .CodeMirror-cursor { border-left: 2px solid #00d9ff !important; }
        .CodeMirror-selected { background: rgba(0, 217, 255, .12) !important; }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #3f3f46; }
    </style>
</head>

<?php
/* ── Detect CodeMirror mode from file extension ── */
$ext  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$mode = match ($ext) {
    'php'                          => 'application/x-httpd-php',
    'js', 'ts', 'mjs'             => 'javascript',
    'jsx', 'tsx'                  => 'text/jsx',
    'css', 'scss', 'sass', 'less' => 'text/css',
    'html', 'htm'                 => 'htmlmixed',
    'json'                        => 'application/json',
    'sh', 'bash', 'zsh'           => 'shell',
    'sql'                         => 'text/x-sql',
    'yaml', 'yml'                 => 'text/x-yaml',
    'xml'                         => 'application/xml',
    default                       => 'text/plain',
};

$backPath = dirname($path) === '.' ? '' : dirname($path);
$backHref = '/btv/scy/file-manager?path=' . rawurlencode($backPath);
?>

<body class="bg-base text-white font-sans text-[13px] flex flex-col h-screen overflow-hidden antialiased">

<!-- ═══════════════════════════════════════════════════════════════
     TOP BAR
════════════════════════════════════════════════════════════════ -->
<header class="h-[52px] bg-panel border-b border-border flex items-center gap-3 px-5 flex-shrink-0">

    <!-- Back -->
    <a href="<?= htmlspecialchars($backHref, ENT_QUOTES, 'UTF-8') ?>"
       class="flex items-center justify-center w-8 h-8 rounded-lg border border-border bg-hover
              text-dim hover:text-zinc-200 hover:border-zinc-500 transition-colors">
        <i class="ri-arrow-left-s-line text-[16px]"></i>
    </a>

    <!-- Logo -->
    <a href="/btv/scy/file-manager?path="
       class="flex items-center gap-2 text-dim hover:text-zinc-200 transition-colors">
        <i class="ri-box-3-fill text-accent"></i>
        <span class="font-semibold text-[14px] tracking-tight text-zinc-200">SCY Core</span>
    </a>

    <span class="text-muted text-[13px]">/</span>

    <!-- File path -->
    <div class="flex items-center gap-2 flex-1 min-w-0">
        <span class="w-6 h-6 rounded-md bg-purple-500/10 flex items-center justify-center flex-shrink-0">
            <i class="ri-file-code-line text-purple-400 text-[13px]"></i>
        </span>
        <code class="font-mono text-[12px] text-zinc-300 truncate">
            <?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>
        </code>

        <!-- Unsaved indicator (hidden by default) -->
        <span id="unsavedDot"
              class="hidden w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"
              title="Unsaved changes"></span>
    </div>

    <!-- Right actions -->
    <div class="flex items-center gap-2">

        <!-- Line / Col display -->
        <div id="cursorPos"
             class="hidden sm:flex items-center gap-1 text-[11px] text-muted font-mono
                    bg-hover border border-border rounded-md px-2.5 py-1">
            Ln 1, Col 1
        </div>

        <!-- Language badge -->
        <span class="text-[11px] font-mono uppercase text-muted bg-hover border border-border
                     rounded-md px-2.5 py-1">
            <?= htmlspecialchars($ext ?: 'txt', ENT_QUOTES, 'UTF-8') ?>
        </span>

        <!-- Save (Ctrl+S) -->
        <button id="saveBtn"
                class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-accent text-black
                       text-[12px] font-semibold hover:bg-[#00c4e8] transition-colors">
            <i class="ri-save-2-line text-[14px]"></i>
            Save
            <span class="text-[10px] opacity-60 hidden sm:inline">Ctrl+S</span>
        </button>

    </div>
</header>

<!-- ═══════════════════════════════════════════════════════════════
     EDITOR BODY — split: gutter | code | info panel
════════════════════════════════════════════════════════════════ -->
<div class="flex flex-1 overflow-hidden">

    <!-- ── Code editor ── -->
    <div class="flex-1 overflow-hidden flex flex-col">
        <form id="editorForm" method="POST" action="/btv/scy/file-manager/save"
              class="flex-1 flex flex-col overflow-hidden">
            <input type="hidden" name="file"
                   value="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">

            <!-- Hidden textarea synced with CodeMirror -->
            <textarea id="codeArea" name="content" class="hidden">
                <?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?>
            </textarea>

            <!-- CodeMirror mounts here -->
            <div id="editorMount" class="flex-1 overflow-hidden"></div>
        </form>
    </div>

    <!-- ── Right info panel ── -->
    <aside class="w-[200px] bg-panel border-l border-border flex flex-col flex-shrink-0
                  overflow-y-auto">

        <div class="p-4 border-b border-border">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-muted mb-3">
                File Info
            </p>
            <dl class="space-y-2.5">
                <div>
                    <dt class="text-[10px] text-muted uppercase tracking-wider">Name</dt>
                    <dd class="text-[12px] text-zinc-300 font-medium truncate mt-0.5"
                        title="<?= htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-[10px] text-muted uppercase tracking-wider">Path</dt>
                    <dd class="text-[11px] text-dim font-mono truncate mt-0.5"
                        title="/<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
                        /<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-[10px] text-muted uppercase tracking-wider">Language</dt>
                    <dd class="text-[12px] text-zinc-300 mt-0.5 uppercase font-mono">
                        <?= htmlspecialchars($ext ?: 'plain', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-[10px] text-muted uppercase tracking-wider">Lines</dt>
                    <dd id="lineCount" class="text-[12px] text-zinc-300 mt-0.5">—</dd>
                </div>
                <div>
                    <dt class="text-[10px] text-muted uppercase tracking-wider">Characters</dt>
                    <dd id="charCount" class="text-[12px] text-zinc-300 mt-0.5">—</dd>
                </div>
            </dl>
        </div>

        <div class="p-4 border-b border-border">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-muted mb-3">
                Editor
            </p>
            <div class="space-y-2">
                <label class="flex items-center justify-between text-[12px] text-dim cursor-pointer">
                    <span>Wrap lines</span>
                    <button id="toggleWrap"
                            class="relative w-8 h-4 rounded-full bg-border transition-colors"
                            role="switch" aria-checked="false">
                        <span class="absolute left-0.5 top-0.5 w-3 h-3 rounded-full bg-muted
                                     transition-all toggle-knob"></span>
                    </button>
                </label>
                <label class="flex items-center justify-between text-[12px] text-dim cursor-pointer">
                    <span>Line numbers</span>
                    <button id="toggleLines"
                            class="relative w-8 h-4 rounded-full bg-accent/60 transition-colors"
                            role="switch" aria-checked="true">
                        <span class="absolute right-0.5 top-0.5 w-3 h-3 rounded-full bg-accent
                                     transition-all toggle-knob"></span>
                    </button>
                </label>
            </div>
        </div>

        <div class="p-4">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-muted mb-3">
                Shortcuts
            </p>
            <ul class="space-y-2 text-[11px] text-dim">
                <li class="flex justify-between">
                    <span>Save</span>
                    <kbd class="font-mono text-muted bg-hover border border-border rounded
                                px-1.5 py-0.5 text-[10px]">Ctrl+S</kbd>
                </li>
                <li class="flex justify-between">
                    <span>Comment</span>
                    <kbd class="font-mono text-muted bg-hover border border-border rounded
                                px-1.5 py-0.5 text-[10px]">Ctrl+/</kbd>
                </li>
                <li class="flex justify-between">
                    <span>Find</span>
                    <kbd class="font-mono text-muted bg-hover border border-border rounded
                                px-1.5 py-0.5 text-[10px]">Ctrl+F</kbd>
                </li>
                <li class="flex justify-between">
                    <span>Select all</span>
                    <kbd class="font-mono text-muted bg-hover border border-border rounded
                                px-1.5 py-0.5 text-[10px]">Ctrl+A</kbd>
                </li>
            </ul>
        </div>
    </aside>

</div>

<!-- ═══════════════════════════════════════════════════════════════
     STATUS BAR
════════════════════════════════════════════════════════════════ -->
<footer class="h-[28px] bg-panel border-t border-border flex items-center gap-4 px-5 flex-shrink-0">
    <span class="flex items-center gap-1.5 text-[11px] text-dim">
        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
        Ready
    </span>
    <span class="text-muted text-[11px]">·</span>
    <span id="statusCursor" class="text-[11px] text-dim font-mono">Ln 1, Col 1</span>
    <span class="text-muted text-[11px]">·</span>
    <span class="text-[11px] text-dim">UTF-8</span>
    <span class="text-muted text-[11px]">·</span>
    <span class="text-[11px] text-dim">LF</span>
    <div class="ml-auto flex items-center gap-3">
        <span id="statusSaved" class="text-[11px] text-green-400 hidden">
            <i class="ri-check-line"></i> Saved
        </span>
        <span id="statusUnsaved" class="text-[11px] text-amber-400 hidden">
            <i class="ri-circle-fill text-[8px]"></i> Unsaved changes
        </span>
    </div>
</footer>

<script>
/* ── CodeMirror initialisation ── */
const textarea = document.getElementById('codeArea');

const editor = CodeMirror(document.getElementById('editorMount'), {
    value:              textarea.value,
    mode:               '<?= $mode ?>',
    theme:              'one-dark',
    lineNumbers:        true,
    matchBrackets:      true,
    autoCloseBrackets:  true,
    styleActiveLine:    true,
    lineWrapping:       false,
    indentUnit:         4,
    tabSize:            4,
    indentWithTabs:     false,
    extraKeys: {
        /* Save with Ctrl+S */
        'Ctrl-S': () => submitForm(),
        /* Toggle comment with Ctrl+/ */
        'Ctrl-/': cm => cm.execCommand('toggleComment'),
    },
});

/* Sync editor → hidden textarea before submit */
document.getElementById('editorForm').addEventListener('submit', () => {
    textarea.value = editor.getValue();
});

function submitForm() {
    textarea.value = editor.getValue();
    document.getElementById('editorForm').submit();
}

/* ── Unsaved-changes tracking ── */
let saved = true;

editor.on('change', () => {
    if (saved) {
        saved = false;
        document.getElementById('unsavedDot').classList.remove('hidden');
        document.getElementById('statusUnsaved').classList.remove('hidden');
        document.getElementById('statusSaved').classList.add('hidden');
    }
    updateStats();
});

document.getElementById('saveBtn').addEventListener('click', submitForm);

/* Show saved state after form submission would reload — mark optimistically */
window.addEventListener('beforeunload', () => { saved = true; });

/* ── Cursor position ── */
function updateCursor() {
    const cur  = editor.getCursor();
    const text = `Ln ${cur.line + 1}, Col ${cur.ch + 1}`;
    document.getElementById('cursorPos').textContent    = text;
    document.getElementById('statusCursor').textContent = text;
    document.getElementById('cursorPos').classList.remove('hidden');
}

editor.on('cursorActivity', updateCursor);
updateCursor();

/* ── Stats (lines / chars) ── */
function updateStats() {
    const val = editor.getValue();
    document.getElementById('lineCount').textContent = editor.lineCount().toLocaleString();
    document.getElementById('charCount').textContent = val.length.toLocaleString();
}

updateStats();

/* ── Toggle: line wrap ── */
const wrapBtn = document.getElementById('toggleWrap');
let   wrapped = false;

wrapBtn.addEventListener('click', () => {
    wrapped = !wrapped;
    editor.setOption('lineWrapping', wrapped);
    wrapBtn.setAttribute('aria-checked', wrapped);
    wrapBtn.classList.toggle('bg-accent/60', wrapped);
    wrapBtn.classList.toggle('bg-border',    !wrapped);
    wrapBtn.querySelector('.toggle-knob').classList.toggle('right-0.5', wrapped);
    wrapBtn.querySelector('.toggle-knob').classList.toggle('left-0.5',  !wrapped);
    wrapBtn.querySelector('.toggle-knob').classList.toggle('bg-accent',  wrapped);
    wrapBtn.querySelector('.toggle-knob').classList.toggle('bg-muted',   !wrapped);
});

/* ── Toggle: line numbers ── */
const linesBtn = document.getElementById('toggleLines');
let   showLines = true;

linesBtn.addEventListener('click', () => {
    showLines = !showLines;
    editor.setOption('lineNumbers', showLines);
    linesBtn.setAttribute('aria-checked', showLines);
    linesBtn.classList.toggle('bg-accent/60', showLines);
    linesBtn.classList.toggle('bg-border',    !showLines);
    linesBtn.querySelector('.toggle-knob').classList.toggle('right-0.5', showLines);
    linesBtn.querySelector('.toggle-knob').classList.toggle('left-0.5',  !showLines);
    linesBtn.querySelector('.toggle-knob').classList.toggle('bg-accent',  showLines);
    linesBtn.querySelector('.toggle-knob').classList.toggle('bg-muted',   !showLines);
});

/* ── Refresh editor after layout is stable ── */
setTimeout(() => editor.refresh(), 50);
</script>

</body>
</html>