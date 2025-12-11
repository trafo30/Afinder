<?php echo "<!-- PROBE ".__FILE__." ".date('H:i:s')." -->"; ?>
<?php
session_start();
?>

<?php
$pageTitle = "AutoFinder";          // opcional, cambia título por página
include 'partials/header.php';      // mete head + modal + header
?>

    <!-- Filtro -->
    <div class="main">
        
        <aside class="sidebar">
            <ul>
                <li><a href="productos.php?cat=baterias">Baterías para autos</a></li>
                <li><a href="productos.php?cat=Aceite">Aceite</a></li>
                <li><a href="productos.php?cat=Aditivos">Aditivos</a></li>
                <li><a href="productos.php?cat=Accesorios para Exterior">Accesorios para exterior</a></li>
                <li><a href="productos.php?cat=Accesorios para interior">Accesorios para interiores</a></li>
                <li><a href="productos.php?cat=rodamientos">Rodamientos</a></li>
                <li><a href="productos.php?cat=filtro-aire">Filtro de aire</a></li>
                <li><a href="productos.php?cat=espejos">Espejos laterales</a></li>  
                <li><a href="productos.php?cat=llantas">Llantas</a></li>
            </ul>
            </aside>

        <!-- Principal -->
        <section class="slider">
            <div class="banner">
                <h1>Compara y ahorra</h1>
                <p>En AutoFinder, comparar precios es muy sencillo. Descubre la mejor alternativa y optimiza tu tiempo y
                    tu dinero.</p>
                <a class="btn" href="productos.php?cat=llantas">Compra Ahora →</a>
            </div>
        </section>
    </div>

    <!-- Seccion : HOY -->
    <section class="ventas">
        <h3>TOP DE HOY</h3>
        <h4>Las mejores ofertas del día</h4>
        <div class="productos">
            <div class="producto">
                <img src="imgs/foto1.1.jpg" alt="Llanta">
                <p class="marca">AUTOSTYLE</p>
                <p><strong>Llanta 185 70R14 88T Z-108</strong></p>
                <p>S/ 109.90 <span class="tachado">S/ 164.90</span></p>
                <div class="botones-producto">
                    <a href="https://sodimac.falabella.com.pe/sodimac-pe/product/127493531/Llanta-185-70R14-88T-Z-108/127493538?exp=sodimac"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.2.jpg" alt="Batería">
                <p class="marca">ENERJET</p>
                <p><strong>Batería para Camioneta 13 Placas 13W75 N2</strong></p>
                <p>S/ 279.90 <span class="tachado">S/ 320.00</span></p>
                <div class="botones-producto">
                    <a href="https://sodimac.falabella.com.pe/sodimac-pe/product/113331078/Bateria-para-Camioneta-13-Placas-13W75-N2/113331080?exp=sodimac"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.3.jpg" alt="Hidrolavadora">
                <p class="marca">KARCHER</p>
                <p><strong>Hidrolavadora Practica 1200W 103Bar Karcher</strong></p>
                <p>S/ 199.00 <span class="tachado">S/ 269.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/143352386/Hidrolavadora-Practica-1200W-103Bar-Karcher/143352387"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.4.jpg" alt="Silla de auto">
                <p class="marca">INFANTI</p>
                <p><strong>LB373 Silla Auto Maya Rubi</strong></p>
                <p>S/ 499.00 <span class="tachado">S/ 899.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/126657929/Silla-de-Auto-para-Bebe-%C2%BBMAYA%C2%BB-Ruby/126657930"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Seccion :  SEMANA -->
    <section class="ventas">
        <h3>TOP DE LA SEMANA</h3>
        <h4>Las mejores ofertas de la semana</h4>
        <div class="productos">
            <div class="producto">
                <img src="imgs/foto 2.1.png" alt="Llanta">
                <p class="marca">RYDANZ</p>
                <p><strong>Llanta 205/55Zr17 Roadster R02 </strong></p>
                <p>S/ 484.80 <span class="tachado">S/ 649.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.promart.pe/llanta-205-55zr17-rydanz-roadster-r02-runflat-91w-1000284439/p"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 2.2.png" alt="Llanta">
                <p class="marca">BOSH</p>
                <p><strong>Bateria Bosch 15 Placas EFB LN3 70 AH 680 A</strong></p>
                <p>S/ 947.90 <span class="tachado">S/ 1106.90</span></p>
                <div class="botones-producto">
                    <a href="https://simple.ripley.com.pe/bateria-bosch-15-placas-efb-ln3-70-ah-680-a-pmp00001330150?s=mdco"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.3.jpg" alt="Llanta">
                <p class="marca">KARCHER</p>
                <p><strong>Hidrolavadora Practica 1200W 103Bar Karcher</strong></p>
                <p>S/ 199.00 <span class="tachado">S/ 269.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/143352386/Hidrolavadora-Practica-1200W-103Bar-Karcher/143352387"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 2.3.png" alt="Llanta">
                <p class="marca">TRUPER</p>
                <p><strong>Combo gata lagarto y caja de herramientas</strong></p>
                <p>S/ 499.90 <span class="tachado">S/ 818.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/124267133/Combo-gata-lagarto-caiman-caballete-y-caja-de-herramientas/124267134"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Seccion :  MES -->
    <section class="ventas">
        <h3>TOP DEL MES</h3>
        <h4>Las mejores ofertas del mes</h4>
        <div class="productos">
            <div class="producto">
                <img src="imgs/foto 3.1.png" alt="Llanta">
                <p class="marca">ALLEN SPORTS</p>
                <p><strong>PORTABICICLETAS ALLEN SPORTS USA EZ LOAD XR200 2 BIKES</strong></p>
                <p>S/ 649.00 <span class="tachado">S/ 899.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/121794367/PORTABICICLETAS-ALLEN-SPORTS-USA-EZ-LOAD-XR200-2-BIKES/121794368"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 3.2.png" alt="Llanta">
                <p class="marca">GENERICO</p>
                <p><strong>Porta Placa Modelo Europeo Machu Picchu Negro</strong></p>
                <p>S/ 31.00 <span class="tachado">S/ 56.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/133487075/Porta-Placa-Modelo-Europeo-Machu-Picchu-Negro/133487076"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 3.3.png" alt="Llanta">
                <p class="marca">GOODYEAR</p>
                <p><strong>Pack x2 Llantas GOODYEAR 185 70R14 Direction Tour</strong></p>
                <p>S/ 279.00 <span class="tachado">S/ 436.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.plazavea.com.pe/k0000013782-llanta-goodyear-185-70r14-direction-tour/p"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 3.4.png" alt="Llanta">
                <p class="marca">TRUPER</p>
                <p><strong>Hidrolavadora Eléctrica 1400W 1500 PSI HILA-1500-2 Truper</strong></p>
                <p>S/ 299.00 <span class="tachado">S/ 515.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/114464137/Hidrolavadora-Electrica-1400W-1500-PSI-HILA-1500-2-Truper/114464141"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
        </div>
    </section>

<?php include 'partials/footer.php'; ?>

</html>