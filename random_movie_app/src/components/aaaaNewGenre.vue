<template>
  <v-container pt-0 pr-0 pb-0 pl-0>
    <v-form ref="form" @submit.prevent="submit()">
      <v-text-field
        @blur="closeForm()"
        append-icon="add"
        solo
        ref="input"
        v-model="name"
        placeholder="Name"
        :rules="[ rules.required ]"
      ></v-text-field>
    </v-form>
  </v-container>
</template>

<script>
export default {
  // name: "newList"
  data: () => ({
    name: "",
    rules: {
      required: value => !!value || "Required"
    }
  }),
  methods: {
    submit() {
      this.$store.dispatch("POST_LIST", {
        name: this.name,
        description: 'kazkas'
      })
      .then(response => {
        this.$store.commit("SET_NOTIFICATION", {
          display: true,
          text: "List has been created!",
          alertClass: "success"
        });
        this.name = '';
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