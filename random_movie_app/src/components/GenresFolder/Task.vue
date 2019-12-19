<template>
  <v-list-item ripple v-on:click.prevent="openInfoModal()">
    <v-list-item-action>
      <v-icon color="pink">movie</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>{{ task.name }}</v-list-item-title>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
export default {
  name: "task",
  props: {
    task: Object,
    index: Number
  },
  data: () => ({}),
  methods: {
    openInfoModal() {
      this.$router.push({
        name: "genreMovieInfoModal",
        params: {
          movieId: this.task.movie_id,
          movie: this.task,
          type: "genreMovie"
        }
      });
    }
  }
};
</script>