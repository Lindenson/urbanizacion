#!/usr/bin/env bash
# Genera los ZIP del tema y del plugin, listos para subir a un WordPress
# desde wp-admin (Apariencia → Subir tema  /  Plugins → Subir plugin).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/dist"
mkdir -p "$OUT"
rm -f "$OUT"/*.zip

cd "$ROOT/wordpress/plugins"
zip -r "$OUT/urbanizacion-mvp.zip" urbanizacion-mvp -x '*.DS_Store' >/dev/null
echo "Creado: dist/urbanizacion-mvp.zip"

cd "$ROOT/wordpress/themes"
zip -r "$OUT/urbanizacion-theme.zip" urbanizacion -x '*.DS_Store' >/dev/null
echo "Creado: dist/urbanizacion-theme.zip"

echo "Listo. Sube estos ZIP desde el panel de WordPress."
