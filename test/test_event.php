<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulaire Slider</title>
</head>
<body>

  <h1>Formulaire Slider</h1>

  <form id="sliderForm">

    <div>
      <label for="titre">Titre:</label>
      
      <input type="text" id="titre" name="titre" required>
    </div>

    <div>

      <label for="description">Description:</label>

      <textarea id="description" name="description" required></textarea>

    </div>

    <div>

      <label for="date">Date:</label>
      <input type="date" id="date" name="date" required>

    </div>

    <div>

      <label for="address">Adresse:</label>
      <input type="text" id="address" name="address" required> 

    </div>
   
    <div>

      <label for="image1">Image 1:</label>
      <input type="file" id="image1" name="image1" accept="image/*" required>

    </div>
   
    <button type="submit">Soumettre</button>

  </form>

  <script>
    document.getElementById('sliderForm').addEventListener('submit', function(event) {
      event.preventDefault();

      // Récupération des valeurs du formulaire
      const titre = document.getElementById('titre').value;
      const description = document.getElementById('description').value;
      const date = document.getElementById('date').value;
      const address = document.getElementById('address').value;
      const imageInput1 = document.getElementById('image1');
      const image1 = imageInput1.files[0];

      // Vérification de tous les champs obligatoires
      if (!titre || !description || !date || !address || !image1) {
        alert('Veuillez remplir tous les champs et sélectionner une image.');
        return;
      }

      // Fonction pour lire le fichier image et le convertir en Base64
      function readFileAsDataURL(file) {
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = function(e) {
            resolve(e.target.result); // Renvoie les données en Base64
          };
          reader.onerror = function(error) {
            reject(error);
          };
          reader.readAsDataURL(file); // Lit le fichier en tant que DataURL (Base64)
        });
      }

      // Lecture du fichier image
      readFileAsDataURL(image1)
        .then(base64Image => {
          // Préparation des données à envoyer
          const data = {
            title: titre,
            description: description,
            date: date,
            address: address,
            image: base64Image
          };

          console.log('Données à envoyer:', data);

          // Paramètres de la requête
          const settings = {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          };

          // Envoi de la requête avec fetch
          return fetch('http://localhost/Elonga_evrnt_api/index.php', settings);
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Erreur de réseau');
          }
          return response.json();
        })
        .then(data => {
          console.log('Réponse du serveur:', data);
          alert('Données envoyées avec succès !');
        })
        .catch(error => {
          alert('Erreur: ' + error.message);
          console.error('Erreur:', error);
        });
    });
  </script>
</body>
</html>
