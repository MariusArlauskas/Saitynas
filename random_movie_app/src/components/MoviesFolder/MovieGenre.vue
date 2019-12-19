<template>
  <v-list-item ripple v-on:click.prevent="openInfoModal()">
    <v-list-item-action>
      <v-icon color="pink">description</v-icon>
    </v-list-item-action>

    <v-list-item-content>
      <v-list-item-title>{{ movieGenre.name }}</v-list-item-title>
    </v-list-item-content>

    <v-btn icon>
      <v-icon color="red" v-on:click.prevent="removeGenre()">delete</v-icon>
    </v-btn>
  </v-list-item>
</template>

<script>
export default {
  name: "movieGenre",
  props: {
    movieGenre: Object
  },
  data: () => ({}),
  methods: {
    openInfoModal() {
      this.$router.push({
        name: "movieGenreInfoModal",
        params: {
          genreId: this.movieGenre.genre_id,
          genre: this.movieGenre
        }
      });
    },
    removeGenre() {
      this.$store
        .dispatch("REMOVE_MOVIE_GENRE", {
          genreId: this.movieGenre.genre_id,
          movieId: this.$route.params.id
        })
        .then(() => {
          this.$store.commit("SET_NOTIFICATION", {
            display: true,
            text: "Genre has been removed!",
            alertClass: "warning"
          })
        })
    }
  }
};
</script>