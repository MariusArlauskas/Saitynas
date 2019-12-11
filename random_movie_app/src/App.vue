<template>
  <v-app>
    <Todo />
  </v-app>
</template>

<script>
// import Login from "./components/Auth/Login";
// import Signup from "./components/Auth/Signup";
import Todo from "./components/Todo";
export default {
  name: "App",
  components: {
    Todo
  },

  data: () => ({
    //
  })
};
</script>
