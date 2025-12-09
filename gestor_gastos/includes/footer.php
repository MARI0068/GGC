<footer class="footer-min">

<!-- REDES SOCIALES -->

    <div class="footer-min-social">
        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" aria-label="X"><i class="fab fa-x-twitter"></i></a>
        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
    </div>


<!-- ENLACES DOCUMENTACIÓN -->   

    <div class="footer-links">
        <a href="privacidad.php">Política de Privacidad</a>
        <a href="aviso_legal.php">Aviso Legal</a>
        <a href="terminos_uso.php">Términos de Uso</a>
        <a href="cookies.php">Cookies</a>
        <a href="contacto.php">Contacto</a>
    </div>


<!-- FIRMA -->    

    <div class="footer-min-inner">
        <span class="footer-project">
            © 2025 GGC - Proyecto DAW 2025 · María del Carmen Navarro Blasco
        </span>
    </div>

</footer>


<!-- AYUDA FLOTANTE -->

<div class="ayuda-flotante ayuda-cerrada" id="ayudaFlotante">
    <div class="ayuda-cabecera">
        <button type="button" class="ayuda-toggle" aria-label="Abrir ayuda">
            ¿Necesitas ayuda?
        </button>
    </div>

    <div class="ayuda-cuerpo">
        <p>Si tienes dudas sobre el registro o el uso de GGC, pulsa aquí.</p>
        <a href="contacto.php" class="ayuda-boton">Ir a contacto</a>
    </div>
</div>


<!-- ABRIR Y CERRAR AYUDA FLOTANTE -->
 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ayuda = document.getElementById('ayudaFlotante');
    if (!ayuda) return;

    const toggle = ayuda.querySelector('.ayuda-toggle');
    if (!toggle) return;

    toggle.addEventListener('click', function () {
        ayuda.classList.toggle('ayuda-cerrada');
    });
});
</script>

</body>
</html>
