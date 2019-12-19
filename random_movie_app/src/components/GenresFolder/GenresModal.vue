<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="60%">
      <v-card>
        <v-card-title>
          <span>{{ currentGenre["name"] }}</span>
        </v-card-title>

        <v-card-text>
          <v-container style="height: 40%; width: 94%" class="ml-5 mr-5">
            <v-row>
              <v-text-field
                class="mt-2"
                label="Title"
                outlined
                v-model="title"
                v-bind:placeholder="currentGenre['name']"
              ></v-text-field>
            </v-row>
            <v-row>
              <v-textarea
                label="Description"
                outlined
                clearable
                rows="10"
                v-model="description"
                v-bind:placeholder="currentGenre['description']"
              ></v-textarea>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn text @click.stop="open = false" color="primary">Close</v-btn>
          <v-spacer></v-spacer>
          <v-btn text @click.stop="upateGenre()" color="red" dark>Save changes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
export default {
  name: "genresModal",
  data: () => ({
    open: true,

    title: "",
    description: ""
  }),
  computed: {
    currentGenre() {
      return this.$store.getters.LIST(this.$route.params.id);
    }
  },
  methods: {
    upateGenre() {
      this.$store
        .dispatch("UPDATE_GENRE", {
          id: this.currentGenre["id"],
          name: this.title,
          description: this.description
        })
        .then(() => {
          this.$store.commit("SET_NOTIFICATION", {
            display: true,
            text: "Genre has been updated!",
            alertClass: "success"
          });
          this.title = "";
          this.description = "";

          this.open = false;
          this.$store.dispatch("GET_LISTS");
          this.$router.push({
            name: "genres",
            params:{
              id: this.currentGenre['id']
            }
          });
        });
    }
  },
  watch: {
    open: function(value) {
      if (value == false) {
        this.$router.push({
          name: "genres",
          params: {
            id: this.$route.params.id
          }
        });
      }
    }
  }
};
</script>