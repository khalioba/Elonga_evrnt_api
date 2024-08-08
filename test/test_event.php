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
      <label for="image1">Image 1:</label>
      <input type="file" id="image1" name="image1" accept="image/*" required>
    </div>
   
    <button type="submit">Soumettre</button>
  </form>

  <script>
   document.getElementById('sliderForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire

  // Récupération des valeurs du formulaire
  var titre = document.getElementById('titre').value;
  var description = document.getElementById('description').value;
  var imageInput1 = document.getElementById('image1');
  var image1 = imageInput1.files[0];

  // Vérification que les fichiers images sont bien sélectionnés
  if (!image1) {
    alert('Veuillez sélectionner une image'); // Alerte pour l'image manquante
    console.error('Image manquante');
    return;
  }

  // Fonction pour lire le fichier image et préparer les données
  function readFileAsArrayBuffer(file) {
    return new Promise((resolve, reject) => {
      var reader = new FileReader();
      reader.onload = function(e) {
        resolve(e.target.result); // Renvoie les données binaires de l'image
      };
      reader.onerror = function(error) {
        reject(error);
      };
      reader.readAsArrayBuffer(file); // Lit le fichier en tant qu'ArrayBuffer
    });
  }

  // Lecture du fichier image
  readFileAsArrayBuffer(image1)
    .then(buffer => {
      // Conversion des données binaires en tableau Uint8
      var imageData = new Uint8Array(buffer);

      // Préparation des données à envoyer
      var data = {
        title: titre,
        description: description,
        image: imageData
      };

      console.log('Données à envoyer:', data); // Message de débogage

      // Paramètres de la requête
      var settings = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      };

      // Exécution de la requête avec fetch
      return fetch('http://localhost/Elonga_evrnt_api/index.php', settings);
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Erreur de réseau'); // Gérer les erreurs HTTP
      }
      return response.json(); // Convertit la réponse en JSON
    })
    .then(data => {
      console.log('Réponse du serveur:', data); // Affiche la réponse dans la console
      alert('Données envoyées avec succès !'); // Alerte de succès
    })
    .catch(error => {
      alert('Erreur: ' + error.message); // Alerte pour les erreurs
      console.error('Erreur:', error); // Affiche les erreurs dans la console
    });
});

  </script>
</body>
</html>
