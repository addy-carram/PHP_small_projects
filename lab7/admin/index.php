<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Admin Panel</title>
    <link rel="stylesheet" href="stylesheet/style.css" />
  </head>
  <body>
    <header>
      <img src="../images/logo.jpg" alt="Logo" />
      <nav>
        <a href="index.php">Acasă</a>
        <a href="products.php">Produse</a>
        <a href="categories.php">Categorii</a>
      </nav>
    </header>
    <main>
      <div id="produs">
        <h2>Produse</h2>
        <p>Vizualizare, adăugare, modificare și ștergere produse.</p>
        <a href="products.php"><button>Administrare produse</button></a>
      </div>
      <div id="categorie">
        <h2>Categorii</h2>
        <p>Vizualizare, adăugare, modificare și ștergere categorii.</p>
        <a href="categories.php"><button>Administrare categorii</button></a>
      </div>
    </main>


    
<script>
function cautaProduse() {
    const cautare = document.getElementById("search")
        .value.toLowerCase().trim();

    const randuri = document.querySelectorAll(
        "#tabelProduse tbody tr"
    );

    let vizibile = 0;

    randuri.forEach(function(rand) {
        const celule = rand.querySelectorAll("td");

        const text = [
            celule[0].textContent,
            celule[1].textContent,
            celule[2].textContent
        ].join(" ").toLowerCase();

        const gasit = text.includes(cautare);

        rand.style.display = gasit ? "" : "none";

        if (gasit) {
            vizibile++;
        }
    });

    document.getElementById("counter").textContent =
        vizibile + " elevi";

    document.getElementById("empty").style.display =
        vizibile === 0 ? "block" : "none";
}
</script>
  </body>

</html>
