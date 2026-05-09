<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCY Core | <?= htmlspecialchars($path ?: 'root', ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        base:    '#09090b',
                        panel:   '#0f0f12',
                        row:     '#131316',
                        hover:   '#1a1a1f',
                        border:  '#27272a',
                        accent:  '#00d9ff',
                        dim:     '#71717a',
                        muted:   '#3f3f46',
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
    <style>
        /* Tailwind scrollbar override */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #3f3f46; }
        [x-cloak] { display: none; }
    </style>
</head>

<body class="bg-base text-zinc-200 font-sans text-[13px] flex h-screen overflow-hidden antialiased">

<!-- ═══════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════════ -->
<aside class="w-[220px] bg-panel border-r border-border flex flex-col flex-shrink-0">

    <!-- Logo -->
    <div class="h-[52px] flex items-center gap-2.5 px-4 border-b border-border flex-shrink-0">
        <i class="ri-box-3-fill text-accent text-lg"></i>
        <span class="font-semibold text-[15px] tracking-tight">SCY Core</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 p-2.5 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-muted px-2 py-2">Explorer</p>

        <a href="/btv/scy/file-manager?path="
           class="flex items-center gap-2.5 px-2.5 py-[7px] rounded-lg bg-accent/10 text-accent text-[12.5px] font-medium">
            <i class="ri-folder-6-fill text-[15px]"></i> File Manager
        </a>

        <p class="text-[10px] font-semibold uppercase tracking-widest text-muted px-2 pt-4 pb-2">System</p>

        <a href="#"
           class="flex items-center gap-2.5 px-2.5 py-[7px] rounded-lg text-dim hover:bg-hover hover:text-zinc-200 transition-colors text-[12.5px]">
            <i class="ri-terminal-box-line text-[15px]"></i> Terminal
        </a>
        <a href="#"
           class="flex items-center gap-2.5 px-2.5 py-[7px] rounded-lg text-dim hover:bg-hover hover:text-zinc-200 transition-colors text-[12.5px]">
            <i class="ri-settings-4-line text-[15px]"></i> Settings
        </a>
    </nav>

    <!-- Disk Usage -->
    <div class="p-3 border-t border-border">
        <div class="flex justify-between text-[11px] text-dim mb-1.5">
            <span>Free space</span>
            <span class="text-zinc-400"><?= htmlspecialchars($diskUsage, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="h-[3px] bg-border rounded-full overflow-hidden">
            <div class="h-full w-[38%] bg-accent rounded-full"></div>
        </div>
    </div>

</aside>

<!-- ═══════════════════════════════════════════
     MAIN
════════════════════════════════════════════ -->
<main class="flex-1 flex flex-col overflow-hidden min-w-0">

    <!-- Top Bar -->
    <header class="h-[52px] bg-panel border-b border-border flex items-center gap-3 px-5 flex-shrink-0">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-1 flex-1 overflow-hidden text-[12px] text-dim" aria-label="Breadcrumb">
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <?php if ($i > 0): ?>
                    <i class="ri-arrow-right-s-line text-muted text-[11px]"></i>
                <?php endif; ?>
                <?php if ($i === count($breadcrumbs) - 1): ?>
                    <span class="text-zinc-300 font-medium truncate"><?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($crumb['href'], ENT_QUOTES, 'UTF-8') ?>"
                       class="text-accent hover:opacity-75 transition-opacity truncate">
                        <?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <!-- Search -->
        <div class="relative">
            <i class="ri-search-line absolute left-2.5 top-1/2 -translate-y-1/2 text-muted text-[12px] pointer-events-none"></i>
            <input id="searchInput" type="search" placeholder="Filter files…"
                   class="bg-base border border-border rounded-lg text-zinc-200 pl-8 pr-3 py-1.5 w-48 text-[12px] outline-none
                          focus:border-accent focus:ring-1 focus:ring-accent/30 placeholder:text-muted transition-all">
        </div>

        <!-- Upload -->
        <form method="POST" action="/btv/scy/file-manager/upload" enctype="multipart/form-data" id="uploadForm">
            <input type="hidden" name="path" value="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-accent text-black text-[12px] font-semibold cursor-pointer hover:bg-[#00c4e8] transition-colors">
                <i class="ri-upload-cloud-2-line text-[14px]"></i> Upload
                <input type="file" name="file" class="hidden" onchange="document.getElementById('uploadForm').submit()">
            </label>
        </form>

    </header>

    <!-- Sub Bar: path display + new folder -->
    <div class="flex items-center gap-3 px-5 py-2.5 border-b border-white/[.04] flex-shrink-0">
        <span class="font-mono text-[11px] text-dim bg-hover border border-border rounded-md px-2.5 py-1 flex-1 overflow-hidden text-ellipsis whitespace-nowrap">
            /<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>
        </span>

        <!-- New Folder modal trigger -->
        <button onclick="document.getElementById('newFolderModal').classList.remove('hidden')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border bg-hover text-zinc-300 text-[12px] font-medium hover:border-zinc-500 transition-colors">
            <i class="ri-folder-add-line text-[14px]"></i> New Folder
        </button>

        <div class="flex items-center gap-1 text-[11px] text-muted">
            <i class="ri-database-2-line"></i>
            <span><?= count($items) ?> items</span>
        </div>
    </div>

    <!-- File Table -->
    <div class="flex-1 overflow-y-auto px-5 pb-5">
        <table class="w-full border-collapse" id="fileTable">
            <thead class="sticky top-0 z-10 bg-base">
                <tr class="border-b border-border">
                    <th class="text-left py-3 px-2 text-[11px] font-medium uppercase tracking-widest text-muted w-1/2">Name</th>
                    <th class="text-left py-3 px-2 text-[11px] font-medium uppercase tracking-widest text-muted">Modified</th>
                    <th class="text-left py-3 px-2 text-[11px] font-medium uppercase tracking-widest text-muted">Size</th>
                    <th class="text-left py-3 px-2 text-[11px] font-medium uppercase tracking-widest text-muted">Perms</th>
                    <th class="py-3 px-2 text-right text-[11px] font-medium uppercase tracking-widest text-muted">Actions</th>
                </tr>
            </thead>
            <tbody id="fileBody">

                <?php if ($path !== ''): ?>
                <!-- Parent directory link -->
                <tr class="border-b border-white/[.03] hover:bg-hover/60 transition-colors group">
                    <td class="py-2.5 px-2" colspan="5">
                        <a href="/btv/scy/file-manager?path=<?= rawurlencode(dirname($path) === '.' ? '' : dirname($path)) ?>"
                           class="flex items-center gap-3 text-dim hover:text-zinc-200 transition-colors">
                            <span class="w-7 h-7 rounded-md bg-zinc-800/60 flex items-center justify-center text-[15px] flex-shrink-0">
                                <i class="ri-arrow-up-line"></i>
                            </span>
                            <span class="font-medium">..</span>
                            <span class="text-[11px] ml-1 text-muted">Parent directory</span>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach ($items as $item): ?>
                <tr class="border-b border-white/[.03] hover:bg-hover/60 transition-colors group file-row"
                    data-name="<?= htmlspecialchars(strtolower($item['name']), ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Name -->
                    <td class="py-2.5 px-2">
                        <?php if ($item['isDir']): ?>
                            <a href="/btv/scy/file-manager?path=<?= rawurlencode($item['rel']) ?>"
                               class="flex items-center gap-3 text-zinc-200 hover:text-accent transition-colors">
                                <span class="w-7 h-7 rounded-md bg-yellow-500/10 flex items-center justify-center text-[15px] flex-shrink-0">
                                    <i class="ri-folder-fill text-yellow-400"></i>
                                </span>
                                <span class="font-medium truncate max-w-xs"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                        <?php else: ?>
                            <div class="flex items-center gap-3">
                                <span class="truncate max-w-xs text-zinc-300"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($item['ext']): ?>
                                    <span class="text-[10px] font-mono uppercase text-muted bg-zinc-800 px-1.5 py-0.5 rounded">
                                        <?= htmlspecialchars($item['ext'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>

                    <!-- Modified -->
                    <td class="py-2.5 px-2 text-dim text-[12px] whitespace-nowrap">
                        <?= date('d M Y, H:i', $item['modified']) ?>
                    </td>

                    <!-- Size -->
                    <td class="py-2.5 px-2 font-mono text-[12px] text-dim whitespace-nowrap">
                        <?= htmlspecialchars($item['sizeHuman'], ENT_QUOTES, 'UTF-8') ?>
                    </td>

                    <!-- Perms -->
                    <td class="py-2.5 px-2">
                        <span class="font-mono text-[11px] <?= $item['perms'] === '0777' ? 'text-red-400' : 'text-dim' ?>">
                            <?= htmlspecialchars($item['perms'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="py-2.5 px-2">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">

                            <?php if (!$item['isDir']): ?>
                            <!-- Edit -->
                            <a href="/btv/scy/file-manager/edit?file=<?= rawurlencode($item['rel']) ?>"
                               class="w-7 h-7 rounded-md flex items-center justify-center text-dim hover:text-accent hover:bg-accent/10 transition-colors"
                               title="Edit">
                                <i class="ri-edit-line text-[14px]"></i>
                            </a>

                            <!-- Download -->
                            <a href="/btv/scy/file-manager/download?file=<?= rawurlencode($item['rel']) ?>"
                               class="w-7 h-7 rounded-md flex items-center justify-center text-dim hover:text-zinc-200 hover:bg-hover transition-colors"
                               title="Download">
                                <i class="ri-download-line text-[14px]"></i>
                            </a>
                            <?php endif; ?>

                            <!-- Rename -->
                            <button onclick="openRename('<?= htmlspecialchars($item['rel'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>')"
                                    class="w-7 h-7 rounded-md flex items-center justify-center text-dim hover:text-zinc-200 hover:bg-hover transition-colors"
                                    title="Rename">
                                <i class="ri-input-cursor-move text-[14px]"></i>
                            </button>

                            <?php if (!$item['isDir']): ?>
                            <!-- Delete -->
                            <form method="POST" action="/btv/scy/file-manager/delete"
                                  onsubmit="return confirm('Delete <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>?')">
                                <input type="hidden" name="file" value="<?= htmlspecialchars($item['rel'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit"
                                        class="w-7 h-7 rounded-md flex items-center justify-center text-dim hover:text-red-400 hover:bg-red-400/10 transition-colors"
                                        title="Delete">
                                    <i class="ri-delete-bin-line text-[14px]"></i>
                                </button>
                            </form>
                            <?php endif; ?>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="5" class="py-16 text-center text-dim">
                        <i class="ri-folder-open-line text-4xl block mb-2 text-muted"></i>
                        This directory is empty.
                    </td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

</main>

<!-- ═══════════════════════════════════════════
     MODAL: New Folder
════════════════════════════════════════════ -->
<div id="newFolderModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
    <div class="bg-panel border border-border rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h2 class="text-[15px] font-semibold mb-1">New Folder</h2>
        <p class="text-dim text-[12px] mb-4">Create a new directory in the current location.</p>
        <form method="POST" action="/btv/scy/file-manager/mkdir">
            <input type="hidden" name="path" value="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" name="name" placeholder="folder-name" autofocus
                   class="w-full bg-base border border-border rounded-lg px-3 py-2 text-zinc-200 font-mono text-[13px] outline-none
                          focus:border-accent focus:ring-1 focus:ring-accent/30 placeholder:text-muted mb-4 transition-all">
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('newFolderModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg border border-border bg-hover text-zinc-300 text-[12px] font-medium hover:border-zinc-500 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-accent text-black text-[12px] font-semibold hover:bg-[#00c4e8] transition-colors">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     MODAL: Rename
════════════════════════════════════════════ -->
<div id="renameModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
    <div class="bg-panel border border-border rounded-2xl p-6 w-full max-w-sm shadow-2xl">
        <h2 class="text-[15px] font-semibold mb-1">Rename</h2>
        <p class="text-dim text-[12px] mb-4">Enter a new name for this item.</p>
        <form method="POST" action="/btv/scy/file-manager/rename">
            <input type="hidden" name="file" id="renameFilePath">
            <input type="text" name="name" id="renameInput"
                   class="w-full bg-base border border-border rounded-lg px-3 py-2 text-zinc-200 font-mono text-[13px] outline-none
                          focus:border-accent focus:ring-1 focus:ring-accent/30 mb-4 transition-all">
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('renameModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg border border-border bg-hover text-zinc-300 text-[12px] font-medium hover:border-zinc-500 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-accent text-black text-[12px] font-semibold hover:bg-[#00c4e8] transition-colors">
                    Rename
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    /* ── Live file filter ── */
    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.file-row').forEach(row => {
            row.style.display = row.dataset.name.includes(q) ? '' : 'none';
        });
    });

    /* ── Rename modal ── */
    function openRename(path, name) {
        document.getElementById('renameFilePath').value = path;
        document.getElementById('renameInput').value    = name;
        document.getElementById('renameModal').classList.remove('hidden');
        document.getElementById('renameInput').focus();
        document.getElementById('renameInput').select();
    }

    /* ── Close modals on backdrop click ── */
    ['newFolderModal', 'renameModal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function (e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });

    /* ── Close modals on Escape ── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.getElementById('newFolderModal').classList.add('hidden');
            document.getElementById('renameModal').classList.add('hidden');
        }
    });
</script>

<?php
/* ── Icon helpers (inlined so the view is self-contained) ── */
function self_fileIcon(string $ext): string {
    return match($ext) {
        'php'                       => 'ri-code-s-slash-line text-purple-400',
        'js', 'ts', 'jsx', 'tsx'    => 'ri-javascript-line text-yellow-400',
        'css', 'scss', 'sass'       => 'ri-css3-line text-blue-400',
        'html', 'htm'               => 'ri-html5-line text-orange-400',
        'json', 'yaml', 'yml', 'toml', 'env' => 'ri-braces-line text-green-400',
        'md', 'txt', 'log'          => 'ri-file-text-line text-zinc-400',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp' => 'ri-image-line text-pink-400',
        'zip', 'tar', 'gz', 'rar'   => 'ri-file-zip-line text-amber-400',
        'sh', 'bash', 'zsh'         => 'ri-terminal-line text-emerald-400',
        'sql'                       => 'ri-database-line text-sky-400',
        'pdf'                       => 'ri-file-pdf-line text-red-400',
        default                     => 'ri-file-line text-zinc-500',
    };
}

function self_iconBg(string $ext): string {
    return match($ext) {
        'php'                       => 'bg-purple-500/10',
        'js', 'ts', 'jsx', 'tsx'    => 'bg-yellow-500/10',
        'css', 'scss', 'sass'       => 'bg-blue-500/10',
        'html', 'htm'               => 'bg-orange-500/10',
        'json', 'yaml', 'yml', 'toml', 'env' => 'bg-green-500/10',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp' => 'bg-pink-500/10',
        'zip', 'tar', 'gz', 'rar'   => 'bg-amber-500/10',
        'sh', 'bash', 'zsh'         => 'bg-emerald-500/10',
        'sql'                       => 'bg-sky-500/10',
        'pdf'                       => 'bg-red-500/10',
        default                     => 'bg-zinc-800/60',
    };
}

/* Alias the closured functions above so the blade template can call them cleanly */
function self($fn) {}
?>
</body>
</html>