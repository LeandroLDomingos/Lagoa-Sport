<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'

interface Document {
  id: number
  file_path: string
  type: string
  status: string
  status_label: string
  type_label: string
}

interface Location {
  name: string
  email: string
  cpf: string
}

const props = defineProps<{
  location: Location
  documents: Document[]
}>()

</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Card Title</CardTitle>
            <CardDescription>Card Description</CardDescription>
        </CardHeader>
        <CardContent>
            Card Content
        </CardContent>
        <CardFooter>
            Card Footer
        </CardFooter>
    </Card>
</template>