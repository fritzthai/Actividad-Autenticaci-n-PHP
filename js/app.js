document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('#nav-publico');
    const login = document.querySelector('#vista-login');
    const registro = document.querySelector('#vista-registro');
    const perfil = document.querySelector('#vista-panel');
    const formLogin = document.querySelector('#form-login');
    const formRegistro = document.querySelector('#form-registro');
    const mensaje = document.querySelector('#mensaje');

    function mostrarMensaje(texto, tipo = 'info') {
        mensaje.textContent = texto;
        mensaje.className = texto ? `mensaje-box ${tipo}` : 'mensaje-box';
    }

    function mostrarVista(vista) {
        nav.classList.toggle('oculto', vista === 'perfil');
        login.classList.toggle('oculto', vista !== 'login');
        registro.classList.toggle('oculto', vista !== 'registro');
        perfil.classList.toggle('oculto', vista !== 'perfil');
        document.querySelector('#tab-login').classList.toggle('active', vista === 'login');
        document.querySelector('#tab-registro').classList.toggle('active', vista === 'registro');
        if (vista !== 'perfil') {
            document.querySelector('#user-nombre').textContent = '';
            document.querySelector('#user-email').textContent = '';
        }
    }

    async function comprobarSesion() {
        try {
            const res = await fetch('api/perfil.php');
            const datos = await res.json();
            if (res.ok) {
                document.querySelector('#user-nombre').textContent = datos.usuario.nombre;
                document.querySelector('#user-email').textContent = datos.usuario.email;
                mostrarVista('perfil');
                mostrarMensaje('');
            } else {
                mostrarVista('login');
                mostrarMensaje(res.status === 401 ? '' : datos.mensaje, 'error');
            }
        } catch (error) {
            mostrarVista('login');
            mostrarMensaje('No se pudo consultar la sesión.', 'error');
        }
    }

    document.querySelector('#tab-login').addEventListener('click', () => {
        mostrarVista('login');
        mostrarMensaje('');
    });
    document.querySelector('#tab-registro').addEventListener('click', () => {
        mostrarVista('registro');
        mostrarMensaje('');
    });

    formRegistro.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        try {
            const res = await fetch('api/registro.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    nombre: document.querySelector('#reg-nombre').value.trim(),
                    email: document.querySelector('#reg-email').value.trim(),
                    password: document.querySelector('#reg-pass').value
                })
            });
            const datos = await res.json();
            if (res.ok) {
                formRegistro.reset();
                mostrarVista('login');
            }
            mostrarMensaje(datos.mensaje, res.ok ? 'exito' : 'error');
        } catch (error) {
            mostrarMensaje('No se pudo registrar el usuario.', 'error');
        }
    });

    formLogin.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        try {
            const res = await fetch('api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: document.querySelector('#login-email').value.trim(),
                    password: document.querySelector('#login-pass').value
                })
            });
            const datos = await res.json();
            if (res.ok) {
                formLogin.reset();
                await comprobarSesion();
            } else {
                mostrarMensaje(datos.mensaje, 'error');
            }
        } catch (error) {
            mostrarMensaje('No se pudo iniciar sesión.', 'error');
        }
    });

    comprobarSesion();
});
