<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="60%">
      <v-card>
        <v-card-title>
          <span>{{ currentMovie["name"] }}</span>
        </v-card-title>

        <v-card-text>
          <v-container style="height: 40%; width: 94%" class="ml-5 mr-5">
            <v-row>
              <v-text-field
                class="mt-2"
                label="Title"
                outlined
                v-model="title"
                v-bind:placeholder="currentMovie['name']"
              ></v-text-field>
            </v-row>
            <!-- @blur="closeForm()" -->

            <v-row>
              <v-text-field
                label="Author"
                outlined
                v-model="author"
                v-bind:placeholder="currentMovie['author']"
              ></v-text-field>
            </v-row>
            <v-row>
              <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="release_date"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    outlined
                    v-model="release_date"
                    label="Release date"
                    v-bind:placeholder="currentMovie['release_date']"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="release_date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
                  <v-btn text color="primary" @click="$refs.menu.save(release_date)">OK</v-btn>
                </v-date-picker>
              </v-menu>
            </v-row>
            <v-row>
              <v-textarea
                label="Description"
                outlined
                clearable
                rows="10"
                v-model="description"
                v-bind:placeholder="currentMovie['description']"
              ></v-textarea>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn text @click.stop="open = false" color="primary">Close</v-btn>
          <v-spacer></v-spacer>
          <v-btn text @click.stop="upateMovie()" color="red" dark>Save changes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
export default {
  name: "moviesModal",
  data: () => ({
    open: true,

    title: "",
    author: "",

    release_date: "",
    menu: false,
    modal: false,

    description: ""
  }),
  computed: {
    currentMovie() {
      return this.$store.getters.MOVIE(this.$route.params.id);
    }
  },
  methods: {
    upateMovie() {
      this.$store
        .dispatch("UPDATE_MOVIE", {
          id: this.currentMovie["id"],
          name: this.title,
          author: this.author,
          release_date: this.release_date,
          description: this.description
        })
        .then(() => {
          this.$store.commit("SET_NOTIFICATION", {
            display: true,
            text: "Movie has been updated!",
            alertClass: "success"
          });
          this.title = "";
          this.description = "";
          this.release_date = "";
          this.author = "";

          this.open = false;
          this.$store.dispatch("GET_MOVIES");
          this.$router.push({
            name: "movieGenres",
            params:{
              type: "movies",
              id: this.currentMovie['id']
            }
          });
        });
    }
  },
  watch: {
    open: function(value) {
      if (value == false) {
        this.$router.push({
          name: "movieGenres",
          params: {
            type: "movies",
            id: this.$route.params.id
          }
        });
      }
    }
  }
};
</script>