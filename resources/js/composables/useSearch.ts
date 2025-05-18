import { ref, computed, watch, type Ref } from 'vue'

export function useSearch<T extends Record<string, any>>(
  data: Ref<T[]>,
  keys: (keyof T)[]
) {
  const searchQuery = ref('')
  const searchResults = computed(() => {
    const query = searchQuery.value.toLowerCase().trim()
    if (!query) return data.value

    return data.value.filter((item) =>
      keys.some((key) => {
        const value = item[key]
        return String(value).toLowerCase().includes(query)
      })
    )
  })

  const noResultsFound = computed(() => searchQuery.value && searchResults.value.length === 0)

  return {
    searchQuery,
    searchResults,
    noResultsFound,
  }
}
