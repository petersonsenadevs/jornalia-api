#!/bin/bash
set -e

# Iterar sobre los scripts en /scripts
for script in /scripts/*.sh; do
    if [ -f "$script" ]; then
        echo "Ejecutando $script..."
        bash "$script"
    fi
done

# Iniciar Supervisor
echo "Iniciando Supervisor..."
exec supervisord -c /etc/supervisor/supervisord.conf
