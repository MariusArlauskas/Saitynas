<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="60%">
      <v-card>
        <v-card-title>
          <span>{{ this.$route.params.movie.name }}</span>
        </v-card-title>

        <v-card-text class="pb-0">
          <v-container class="ml-5 mr-5 pb-0">
            <v-row
              class="subtitle-2 font-italic"
            >{{ this.$route.params.movie.author }} || {{ this.$route.params.movie.release_date }}</v-row>
          </v-container>
        </v-card-text>

        <v-card-text>
          <v-container style="height: 40%; width: 94%" class="ml-5 mr-5">
            <v-row
              style="width: 98%; max-height: 77%; height: 77%"
            >{{ this.$route.params.movie.description }}</v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn text @click.stop="open = false" color="primary">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
export default {
  name: "genreMovieInfoModal",
  data: () => ({
    open: true,

    title: "",
    description: ""
  }),
  computed: {},
  methods: {},
  watch: {
    open: function(value) {
      if (value == false) {
        if (this.$route.params.type == "genreMovie") {
          this.$router.push({
            name: "genres",
            params: {
              id: this.$route.params.id
            }
          });
        } else {
          if (this.$route.params.type == "userMovie") {
            this.$router.push({
              name: "userMovies",
              params: {
                id: this.$route.params.id
              }
            });
          }
        }
      }
    }
  }
};
</script>