<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W3css</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body id='bootstrap'>
    <header>
        <h1>Ejemplo de como utilizar bootstrap</h1>
        <img src="img/bootstrap.png" alt="encabezado">
    </header>
    <nav class="navbar navbar-expand-lg bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">Navbar</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="w3css">W3.CSS</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="bootstrap">Bootstrap</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="tailwind">Tailwind</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="react">React</a>
              </li>
            </ul>
            <form class="d-flex" role="search">
              <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
          </div>
        </div>
      </nav>
    <main>
        <main>
            <section>
                <article>
                    <p>Esta pagina es un ejemplo de como utilizar
                        w3css, para crear hojas de estilo.
                    </p>
                </article>
                <article>
                    <h3>Ejemplo Formulario</h3>
                    <form class="row g-3">
                        <div class="col-md-6">
                          <label for="inputEmail4">Email</label>
                          <input type="email" id="inputEmail4">
                        </div>
                        <div class="col-md-6">
                          <label for="inputPassword4">Password</label>
                          <input type="password" id="inputPassword4">
                        </div>
                        <div class="col-12">
                          <label for="inputAddress">Address</label>
                          <input type="text" id="inputAddress" placeholder="1234 Main St">
                        </div>
                        <div class="col-12">
                          <label for="inputAddress2">Address 2</label>
                          <input type="text" id="inputAddress2" placeholder="Apartment, studio, or floor">
                        </div>
                        <div class="col-md-6">
                          <label for="inputCity">City</label>
                          <input type="text" id="inputCity">
                        </div>
                        <div class="col-md-4">
                          <label for="inputState">State</label>
                          <select id="inputState">
                            <option selected>Choose...</option>
                            <option>...</option>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label for="inputZip">Zip</label>
                          <input type="text" id="inputZip">
                        </div>
                        <div class="col-12">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck">
                            <label class="form-check-label" for="gridCheck">
                              Check me out
                            </label>
                          </div>
                        </div>
                        <div class="col-12">
                          <button type="submit">Sign in</button>
                        </div>
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
