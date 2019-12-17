<template>
  <v-container pt-0 pr-0 pb-0 pl-0>
    <v-form ref="form" @submit.prevent="submit()">
      <v-text-field
        class="mt-2"
        label="Title"
        outlined
        ref="input"
        v-model="name"
        placeholder="Title"
        :rules="[ rules.required ]"
      ></v-text-field>
      <!-- @blur="closeForm()" -->
      <v-text-field
        label="Description"
        outlined
        v-model="description"
        placeholder="Description"
      ></v-text-field>
      
      <v-row class="mr-3 ml-3">
      <v-btn text color="primary" @click.prevent="closeForm()">Close</v-btn>
      <v-spacer></v-spacer>
      <v-btn text color="red" @click.prevent="submit()">Submit</v-btn>
      </v-row>
    </v-form>
  </v-container>
</template>

<script>
export default {
  // name: "newList"
  data: () => ({
    name: "",
    description: "",
    rules: {
      required: value => !!value || "Required"
    }
  }),
  methods: {
    submit() {
      if (this.description === "") {
        this.description = "No description";
      }
      this.$store.dispatch("POST_LIST", {
        name: this.name,
        description: this.description
      })
      .then(response => {
        this.$store.commit("SET_NOTIFICATION", {
          display: true,
          text: "Genre has been created!",
          alertClass: "success"
        });
        this.name = '';
        this.description = "";
        this.$router.push({
          name: 'tasks',
          params: {
            id: response.object.id
          }
        })

        this.$store.commit("SET_NEW_LIST_FORM", false);
      });
    },
    closeForm() {
      this.$store.commit("SET_NEW_LIST_FORM", false);
    },
    mounted() {
      this.$refs.input.focus();
    }
  }
};
</script>