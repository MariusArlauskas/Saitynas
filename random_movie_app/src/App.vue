<template>
  <v-app>
    <Login/>
  </v-app>
</template>

<script>
import Login from './components/Auth/Login'

export default {
  name: 'app',
  components: {
    Login
  }
}
</script>
