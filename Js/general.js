// alertas.js
function mostrarToast(tipo, mensaje) {
    Swal.fire({
        icon: tipo,
        title: mensaje,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}
