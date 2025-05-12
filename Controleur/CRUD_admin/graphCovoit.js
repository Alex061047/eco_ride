// Ligne des abscisses, 30 derniers jours
function genererDerniersJours(nbJours) {
    const labels = [];
    const aujourdHui = new Date();
  
    for (let i = nbJours - 1; i >= 0; i--) {
      const date = new Date();
      date.setDate(aujourdHui.getDate() - i);
      // Formatage de la date jour/mois
      const jour = String(date.getDate()).padStart(2, '0');
      const mois = String(date.getMonth() + 1).padStart(2, '0');
      labels.push(`${jour}/${mois}`);
    }
  
    return labels;
  }
  
  // Récupération données du serveur
  async function recupererDonneesReelles(labels) {
    try {
      const response = await fetch("../../Modele/CRUD_admin/graph_covoit.php");
      const data = await response.json();
  
      // Création d'un dictionnaire jour/mois : total
      const dataMap = {};
      data.forEach(item => {
        dataMap[item.jour] = parseInt(item.total);
      });
  
      // Remplit les données selon les labels
      const donnees = labels.map(label => dataMap[label] || 0);
      return donnees;
    } catch (error) {
      console.error("Erreur lors de la récupération des données :", error);
      return labels.map(() => 0);
    }
  }
  
  // Création et affichage du graphique avec Chart.js
  async function afficherGraphique() {
    const canvas = document.getElementById("graphCovoiturages");
    if (!canvas) {
      console.warn("Canvas graphCovoiturages non trouvé.");
      return;
    }
  
    const labels = genererDerniersJours(30);
    const data = await recupererDonneesReelles(labels);
  
    const ctx = canvas.getContext("2d");

    // Création du graphique
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
