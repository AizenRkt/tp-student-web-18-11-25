<script>
export default {
    props: {
        'title': String,
        'todos': Array
    }
};
</script>

<template>
    <h5> {{ title }}</h5>
    <ul v-if="todos.length > 0">
        <li v-for="todo in todos">
        {{ todo }}
        </li>
    </ul>
    <slot v-else="" name="no-todos">
        il n'y pas de todo
    </slot>
</template>