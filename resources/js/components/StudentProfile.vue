<script setup>
import { reactive } from 'vue'
const student = reactive({
    firstname: "John",
    secondname: "Doe",
    admission: "ADM001",
    course: "Diploma in ICT",
    photo: "https://via.placeholder.com/150"
})
</script>
<template>
<img
:src="student.photo"
width="150"
>
<h2>
{{ student.firstname }}
{{ student.secondname }}
</h2>
<p>
Admission:
{{ student.admission }}
</p>
<p>
Course:
{{ student.course }}
</p>
</template>
