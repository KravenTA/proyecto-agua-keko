/**
 * Contador de caracteres (Parcial 2).
 *
 * Uso: agregar el atributo data-contador a cualquier input o textarea
 * que tenga maxlength. Debajo del campo aparece "actual/maximo" y
 * cambia de color segun que tan cerca este del limite:
 *   - Normal (menos del 80%):  gris
 *   - Advertencia (desde 80%): naranja + "Te quedan N"
 *   - Limite (100%):           rojo + "Llegaste al limite"
 */
(function () {
    const PORCENTAJE_AVISO = 0.8;
    const COLOR_NORMAL     = '#8392ab';
    const COLOR_AVISO      = '#e67e22';
    const COLOR_LIMITE     = '#dc3545';

    function iniciar(campo) {
        const maximo = parseInt(campo.getAttribute('maxlength'), 10);
        if (! maximo) return; // sin maxlength no hay nada que contar

        // Se crea el texto del contador justo debajo del campo.
        const contador = document.createElement('small');
        contador.className = 'd-block text-end mt-1';
        contador.style.fontSize = '0.75rem';
        contador.setAttribute('aria-live', 'polite');
        campo.insertAdjacentElement('afterend', contador);

        function actualizar() {
            const actual    = campo.value.length;
            const restantes = maximo - actual;

            if (actual >= maximo) {
                contador.textContent      = actual + '/' + maximo + ' · Llegaste al límite';
                contador.style.color      = COLOR_LIMITE;
                contador.style.fontWeight = '700';
                campo.style.borderColor   = COLOR_LIMITE;

            } else if (actual >= maximo * PORCENTAJE_AVISO) {
                const texto = restantes === 1 ? 'Te queda 1' : 'Te quedan ' + restantes;
                contador.textContent      = actual + '/' + maximo + ' · ' + texto;
                contador.style.color      = COLOR_AVISO;
                contador.style.fontWeight = '700';
                campo.style.borderColor   = COLOR_AVISO;

            } else {
                contador.textContent      = actual + '/' + maximo;
                contador.style.color      = COLOR_NORMAL;
                contador.style.fontWeight = '400';
                campo.style.borderColor   = '';
            }
        }

        campo.addEventListener('input', actualizar);
        actualizar(); // en modo edicion el campo ya trae texto
    }

    document.querySelectorAll('[data-contador]').forEach(iniciar);
})();