<template>
  <v-list-item @click.prevent="openInfoModal(index)">
    <v-list-item-action>
      <v-icon color="pink">movie</v-icon>
    </v-list-item-action>

    <v-list-item-content>
      <v-list-item-title>{{ userMovie.name }}</v-list-item-title>
    </v-list-item-content>

    <v-btn icon>
      <v-icon color="red" v-on:click.stop="removeUserMovie()">delete</v-icon>
    </v-btn>
  </v-list-item>
</template>

<script>
export default {
  name: "userMovie",
  props: {
    userMovie: Object,
    index: Number
  },
  data: () => ({
    selected: [2]
  }),
  methods: {
    openInfoModal() {
        console.log(this.userMovie)
      this.$router.push({
        name: "userMovieInfo",
        params: {
          movieId: this.userMovie.movie_id,
          movie: this.userMovie,
          type: "userMovie"
        }
      });
    },
    removeUserMovie(){
      this.$store
        .dispatch("REMOVE_USER_MOVIE", {
          movieId: this.userMovie.movie_id,
          userId: this.$route.params.id
        })
        .then(() => {
          this.$store.commit("SET_NOTIFICATION", {
            display: true,
            text: "Removed movie from users list!",
            alertClass: "warning"
          })
        })
    }
  }
};
</script>