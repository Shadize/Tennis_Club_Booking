<template>
  <!-- Le template contient le contenu de l'application. -->
  <div id="app">
    <!-- Le div "app" est l'élément racine de notre application. -->
    <router-view />
    <!-- Le composant "router-view" est utilisé pour afficher le contenu de chaque page. -->
  </div>
</template>

<script>
// Le script contient la logique de notre composant.

export default {
  // Le nom du composant est défini ici.
  name: "App",
};
</script>
