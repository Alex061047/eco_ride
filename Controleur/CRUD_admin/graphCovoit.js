// Ligne des abscisses, 30 derniers jours
function genererDerniersJours(nbJours) {
    const labels = [];
    const aujourdHui = new Date();
  
    for (let i = nbJours - 1; i >= 0; i--) {
      const date = new Date();
      date.setDate(aujourdHui.getDate() - i);
      // Formatage de la date jour/ mois
      const jour = String(date.getDate()).padStart(2, '0');
      const mois = String(date.getMonth() + 1).padStart(2, '0');
      labels.push(`${jour}/${mois}`);
    }
  
    return labels;
  }
  
  // Génération de données factice
  function genererDonneesFactices(nbJours) {
    return Array.from({ length: nbJours }, () => Math.floor(Math.random() * 11));
  }
  
  // Création et affichage du graphique avec Chart.js
  function afficherGraphique() {
    const canvas = document.getElementById("graphCovoiturages");
    if (!canvas) {
      console.warn("Canvas graphCovoiturages non trouvé.");
      return;
    }
  
    const labels = genererDerniersJours(30);
    const data = genererDonneesFactices(30);
  
    const ctx = canvas.getContext("2d");
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: 'rgba(75, 192, 192, 0.5)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
          },
          legend: {
            display: false 
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
  }
  
  // Appel pour afficher le graphique dès le chargement
  afficherGraphique();
  