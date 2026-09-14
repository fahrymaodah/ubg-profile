#!/usr/bin/env bash
# Rebuild asset + tampilkan perubahan hash. Jalankan DI LOKAL sebelum commit,
# setiap kali menyentuh resources/css, resources/js, atau class Tailwind di Blade.
set -euo pipefail

cd "$(dirname "$0")"

before=$(git hash-object public/build/manifest.json 2>/dev/null || echo none)
npm run build
after=$(git hash-object public/build/manifest.json)

echo
if [ "$before" = "$after" ]; then
    echo "==> Asset tidak berubah, tidak ada yang perlu di-commit."
else
    echo "==> Asset BERUBAH. Jangan lupa commit public/build:"
    echo "    git add public/build && git commit -m 'build: rebuild asset'"
fi
