<template>
  <v-form>
    <v-text-field v-model="title" solo label="Add new task" append-icon="add"></v-text-field>
  </v-form>
</template>

<script>
export default {
  name: "newTask",
  data: () => ({
    title: ""
  }),
  methods: {
    // async createNewTask() {
    //   await this.$store.dispatch("POST_TASK", {
    //     taskTitle: this.title,
    //     listId: this.$route.params.id
    //   });
    //   this.$store.commit("SET_NOTIFICATION", {
    //     display: true,
    //     text: "Task has been created!",
    //     alertClass: "success"
    //   });
    //   this.title = "";
    // }
  }
};
</script>