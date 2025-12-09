
<?php

$imgDir = dirname(__DIR__) . '/imagenes';
$imgRel = 'imagenes'; 
$exts   = ['jpg','jpeg','png','webp','gif'];
$slides = [];

if (is_dir($imgDir)) {
  foreach (scandir($imgDir) as $f) {
    $path = $imgDir . '/' . $f;
    if (!is_file($path)) continue;
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (!in_array($ext, $exts)) continue;
    $caption = ucwords(trim(preg_replace('/[_\-]+/',' ', pathinfo($f, PATHINFO_FILENAME))));
    $slides[] = ['src' => $imgRel . '/' . $f, 'caption' => $caption ?: 'Imagen'];
  }
}
usort($slides, fn($a,$b) => strnatcasecmp($a['src'],$b['src']));
if (!$slides) { $slides = [['src'=>'imagenes/menu/euro.gif','caption'=>'Gestor de Gastos']]; }
?>

<main class="landing">
  <section class="inicio">
<section class="carousel">
  <div class="slides">
    <?php foreach ($slides as $i => $img): ?>
      <div class="slide <?= $i===0 ? 'active' : '' ?>"
           style="background-image:url('<?= htmlspecialchars($img['src'], ENT_QUOTES) ?>')">
        <div class="caption"><?= htmlspecialchars($img['caption']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <button class="nav prev" aria-label="Anterior">‹</button>
  <button class="nav next" aria-label="Siguiente">›</button>
  <div class="dots"></div>
</section>

<!-- AQUI LA FRANJA CON LA MONEDA -->
<section class="franja-moneda">
  <img src="imagenes/menu/euro.gif" class="moneda" alt="Euro">
</section>

<section class="hero">
  <div class="hero-inner">
    <h1>Bienvenida al Gestor de Gastos Compartidos</h1>
    <p>Organiza los gastos de tu piso, viajes o proyectos en equipo sin complicaciones.</p>
    <div class="hero-actions">
      <a href="grupos.php" class="btn btn-primary">🗂 Ver mis grupos</a>
      <a href="grupos.php?modal=nuevo" class="btn btn-ghost">＋ Crear un grupo</a>
    </div>
  </div>
</section>

</section>


</main>



<script>
(function(){
  const root = document.querySelector('.carousel'); if(!root) return;
  const slides=[...root.querySelectorAll('.slide')];
  const dotsC = root.querySelector('.dots');
  const nextB = root.querySelector('.next');
  const prevB = root.querySelector('.prev');
  let i=0, t;

  slides.forEach((_,idx)=>{
    const b=document.createElement('button');
    if(idx===0) b.classList.add('active');
    b.onclick=()=>go(idx);
    dotsC.appendChild(b);
  });

  function go(n){
    slides[i].classList.remove('active'); dotsC.children[i].classList.remove('active');
    i=(n+slides.length)%slides.length;
    slides[i].classList.add('active'); dotsC.children[i].classList.add('active');
    restart();
  }
  function next(){ go(i+1); }
  function prev(){ go(i-1); }
  function restart(){ clearInterval(t); t=setInterval(next,5000); }

  nextB.onclick=next; prevB.onclick=prev; restart();
})();
</script>

 <script>
    const carousel = document.querySelector('.carousel');
    const slides = document.querySelectorAll('.slide');
    let index = 0;

    function nextSlide() {
      // 1. oscurece
      carousel.classList.add('fade');

      // 2. cambia la imagen mientras está oscuro
      setTimeout(() => {
        slides[index].classList.remove('active');
        index = (index + 1) % slides.length;
        slides[index].classList.add('active');
      }, 1000); // mismo tiempo que el fade-out en CSS

      // 3. aclara
      setTimeout(() => {
        carousel.classList.remove('fade');
      }, 2000); // 1s oscuro + 1s aclarado
    }

    // cambia cada 5 segundos
    setInterval(nextSlide, 5000);
  </script>
