document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour récupérer les paramètres d'URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    
    // Vérifier si un paramètre d'erreur existe
    var error = getUrlParameter('error');
    
    if (error === 'login_failed') {
        createAlert('Email ou mot de passe incorrect');
    } else if (error === 'fields_required') {
        createAlert('Remplissez tous les champs');
    }
    
    function createAlert(message) {
        // Créer une alerte d'erreur
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.style.maxWidth = '380px';
        alertDiv.style.marginBottom = '20px';
        alertDiv.textContent = message;
        
        // Insérer l'alerte avant le formulaire
        var form = document.querySelector('form.login');
        form.parentNode.insertBefore(alertDiv, form);
    }
});