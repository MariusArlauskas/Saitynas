<template>
  <v-form >
    <v-select 
      label="Add genre"
      v-model="value"
      item-text="name"
      item-value="id"
      outlined
      attach
      v-on:change="addNewGenre()"
      v-bind:items="LISTS_TITLES"
      :menu-props="{ top: true, offsetY: true }"
    ></v-select>
  </v-form>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "newMovieGenre",
  data: () => ({
    items: [],
    value: []
  }),
  computed: {
    ...mapGetters(["LISTS_TITLES"]),
  },
  methods: {
    async addNewGenre() {
      await this.$store.dispatch("ADD_GENRE", {
        movieId: this.$route.params.id,
        genreToAdd: this.value.toString()
      });
      this.value = [],
      this.$store.commit("SET_NOTIFICATION", {
        display: true,
        text: "Genre has been added!",
        alertClass: "success"
      })
      // this.$store.dispatch("GET_MOVIES");
      //     this.$router.push({
      //       name: "tasks"
      // });
    }
  }
};
</script>