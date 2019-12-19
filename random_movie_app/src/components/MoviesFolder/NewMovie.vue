<template>
  <v-container pt-0 pr-0 pb-0 pl-0>
    <v-form ref="form">
      <v-text-field
        class="mt-2"
        label="Title"
        outlined
        v-model="name"
        placeholder="Title"
        :rules="[ rules.required ]"
      ></v-text-field>
      <!-- @blur="closeForm()" -->
      <v-text-field
        label="Author"
        outlined
        v-model="author"
        placeholder="Author"
        :rules="[ rules.required ]"
      ></v-text-field>

      <v-menu
        ref="menu"
        v-model="menu"
        :close-on-content-click="false"
        :return-value.sync="release_date"
      >
        <template v-slot:activator="{ on }">
          <v-text-field outlined v-model="release_date" label="Release date" v-on="on"></v-text-field>
        </template>
        <v-date-picker v-model="release_date" no-title scrollable>
          <v-spacer></v-spacer>
          <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
          <v-btn text color="primary" @click="$refs.menu.save(release_date)">OK</v-btn>
        </v-date-picker>
      </v-menu>

      <v-select
        label="Genres"
        v-model="value"
        :items="LISTS_TITLES"
        item-text="name"
        item-value="id"
        outlined
        multiple
        attach
        chips
        placeholder="Genres"
        :rules="[ rules.required ]"
      ></v-select>

      <v-textarea
        label="Description"
        outlined
        clearable
        v-model="description"
        placeholder="Description"
      ></v-textarea>

      <v-row class="mr-3 ml-3">
        <v-btn text color="primary" @click.prevent="closeForm()">Close</v-btn>
        <v-spacer></v-spacer>
        <v-btn text color="red" @click.prevent="submit()">Submit</v-btn>
      </v-row>
    </v-form>
  </v-container>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  // name: "newList"
  data: () => ({
    name: "",
    author: "",

    release_date: new Date().toISOString().substr(0, 10),
    menu: false,
    modal: false,

    items: [],
    value: [],

    genres: "",
    description: "",
    rules: {
      required: value => !!value || "Required"
    }
  }),
  computed: {
    ...mapGetters(["LISTS_TITLES"])
  },
  methods: {
    submit() {
      if (this.valid() === false) {
        this.$store.commit("SET_NOTIFICATION", {
          display: true,
          text: "Movie needs at least a title!",
          alertClass: "red"
        });
        return;
      }
      this.$store
        .dispatch("POST_MOVIE", {
          name: this.name,
          author: this.author,
          release_date: this.release_date,
          description: this.description,
          genreId: this.value
        })
        .then(response => {
          this.$store.commit("SET_NOTIFICATION", {
            display: true,
            text: "Movie has been created!",
            alertClass: "success"
          });
          this.name = "";
          this.description = "";
          this.release_date = new Date().toISOString().substr(0, 10);
          this.author = "";
          this.genres = "";

          this.$router.push({
            name: "movieGenres",
            params: {
              type: "movies",
              id: response.data.id
            }
          });
          this.$store.commit("SET_NEW_MOVIE_FORM", false);
        });
    },
    closeForm() {
      this.$store.commit("SET_NEW_MOVIE_FORM", false);
    },
    mounted() {
      this.$refs.form.focus();
    },
    valid() {
      if (this.name === "") {
        return false;
      }
      if (this.description === "") {
        this.description = "No description";
      }
      if (this.author === "") {
        this.author = "Unknown";
      }
      return true
    }
  }
};
</script>