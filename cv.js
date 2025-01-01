document.getElementById('competenceForm').addEventListener('submit', function(event) {
    event.preventDefault();
    let nom = document.getElementById('nom').value;
    let description = document.getElementById('description').value;
    let niveau = document.getElementById('niveau').value;

    let competences = JSON.parse(localStorage.getItem('competences')) || [];
    competences.push({ nom, description, niveau });
    localStorage.setItem('competences', JSON.stringify(competences));

    afficherCompetences();
    this.reset();
});

function afficherCompetences() {
    let competences = JSON.parse(localStorage.getItem('competences')) || [];
    let liste = document.getElementById('listeCompetences');
    liste.innerHTML = '';

    competences.forEach((competence, index) => {
        let item = document.createElement('div');
        item.innerHTML = `
            <h3>${competence.nom}</h3>
            <p>${competence.description}</p>
            <p>Niveau : ${competence.niveau}</p>
            <button onclick="modifierCompetence(${index})">Modifier</button>
            <button onclick="supprimerCompetence(${index})">Supprimer</button>
        `;
        liste.appendChild(item);
    });
}

function modifierCompetence(index) {
    let competences = JSON.parse(localStorage.getItem('competences'));
    let competence = competences[index];

    document.getElementById('nom').value = competence.nom;
    document.getElementById('description').value = competence.description;
    document.getElementById('niveau').value = competence.niveau;

    document.getElementById('competenceForm').onsubmit = function(event) {
        event.preventDefault();
        competence.nom = document.getElementById('nom').value;
        competence.description = document.getElementById('description').value;
        competence.niveau = document.getElementById('niveau').value;

        localStorage.setItem('competences', JSON.stringify(competences));
        afficherCompetences();
        this.onsubmit = null; // Reset the form submission handler
        this.reset();
    };
}

function supprimerCompetence(index) {
    let competences = JSON.parse(localStorage.getItem('competences'));
    competences.splice(index, 1);
    localStorage.setItem('competences', JSON.stringify(competences));
    afficherCompetences();
}

document.addEventListener('DOMContentLoaded', afficherCompetences);

