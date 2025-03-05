<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W3css</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body id='w3css'>
    <header>
        <h1>Ejemplo de como utilizar w3css</h1>
        <img src="img/w3css.png" alt="encabezado">
    </header>
    <nav>
        <ul>
            <li><a href="w3css">W3.CSS</a></li>
            <li><a href="bootstrap">Bootstrap</a></li>
            <li><a href="tailwind">Tailwind</a></li>
            <li><a href="react">React</a></li>
        </ul>
    </nav>
    <main>
        <main>
            <section>
                <article>
                    <p>Diego
                    </p>
                </article>
                <article>
                    <h3>Ejemplo Formulario</h3>
                    <form action="">
                        <h2 class="w3-center">Contact Us</h2>
                        <div class="w3-row w3-section">
                          <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-user"></i></div>
                            <div class="w3-rest">
                              <input name="first" type="text" placeholder="First Name">
                            </div>
                        </div>

                        <div class="w3-row w3-section">
                          <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-user"></i></div>
                            <div class="w3-rest">
                              <input name="last" type="text" placeholder="Last Name">
                            </div>
                        </div>

                        <div class="w3-row w3-section">
                          <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-envelope-o"></i></div>
                            <div class="w3-rest">
                              <input name="email" type="text" placeholder="Email">
                            </div>
                        </div>

                        <div class="w3-row w3-section">
                          <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-phone"></i></div>
                            <div class="w3-rest">
                              <input name="phone" type="text" placeholder="Phone">
                            </div>
                        </div>

                        <div class="w3-row w3-section">
                          <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-pencil"></i></div>
                            <div class="w3-rest">
                              <input name="message" type="text" placeholder="Message">
                            </div>
                        </div>

                        <button>Send</button>

                        </form>
                </article>
                <article>
                    <h3>Tablas</h3>
                    <table>
                        <thead>
                          <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Points</th>
                          </tr>
                        </thead>
                        <tr>
                          <td>Jill</td>
                          <td>Smith</td>
                          <td>50</td>
                        </tr>
                        <tr>
                          <td>Eve</td>
                          <td>Jackson</td>
                          <td>94</td>
                        </tr>
                        <tr>
                          <td>Adam</td>
                          <td>Johnson</td>
                          <td>67</td>
                        </tr>
                      </table>
                </article>
            </section>
        </main>
        <aside>
            <h2>Enlases</h2>
            <ul>
                <li><a href="https://www.w3schools.com/w3css/default.asp">Sitio w3css</a></li>

            </ul>
        </aside>
    </main>
    <footer>
        <h1>Instituto Tecnologico de La Paz</h1>
        <h2>Departamento de Sistemas y Computacion</h2>
        <h3>Carrera de Ingenieria en Sistemas Computacionales</h3>
        <h4>Materia de Aplicaciones Web I</h4>
    </footer>
</body>
</html>
