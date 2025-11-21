<script>
import Todo from './components/Todo.vue'

export default {
  components: {
    Todo
  },
  data() {
    return {
      items: [
        { title: 'todo Perso', todos: ['faire du thé'] }
      ]
    }
  }
}
</script>

<template>
  <div style="display: block;">
    <h1>{{ title }}</h1>
    <Todo v-for="item in items" :key="item.title" :title="item.title" :todos="item.todos">
      <template v-slot:no-todos>No todos</template>
    </Todo>
  </div>
</template>
