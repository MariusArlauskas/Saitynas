<template>
    <v-list-item @click.prevent="toggle(index)">
        <v-list-item-action>
            <v-btn @click="openModal" icon>
                <v-icon color="pink">movie</v-icon>
            </v-btn>
        </v-list-item-action>

        <v-list-item-content>
            <v-list-item-title>{{ task.name }}</v-list-item-title>
        </v-list-item-content>

        <v-list-item-action-text>
            <v-icon color="green" v-if="task.isComplete">check_circle</v-icon>
        </v-list-item-action-text>
    </v-list-item>
</template>

<script>
export default {
    name: "task",
    props: {
        task: Object,
        index: Number
    },
    data: () => ({
        selected: [2]
    }),
    methods: {
        toggle(index){
            console.log(index)
            // TODO: push changes to the store
        },
        openModal(){
            this.$router.push(
                {
                    name: 'notes',
                    params: { taskId: this.task.id }
                }
            )
        }
    }
}
</script>