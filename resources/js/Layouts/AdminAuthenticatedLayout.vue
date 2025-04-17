<script lang="ts">
  export const description
    = 'A sidebar that collapses to icons.'
  export const iframeHeight = '800px'
  export const containerClass = 'w-full h-full'
</script>

<script setup lang="ts">
  import { usePage } from '@inertiajs/vue3' //use to get the loggged in user attributes
  
  import AppSidebar from '@/Components/adminComponents/AppSidebar.vue'
  import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
  } from '@/Components/ui/breadcrumb'
  import { Separator } from '@/Components/ui/separator'
  import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
  } from '@/Components/ui/sidebar'

const page = usePage() //inirialize the import
const user = page.props.auth.user // then get the logged in user

//Now the layout displays the breadcrumb title based on the page you're on, because it’s getting it from the prop.
const props = defineProps({
  pageTitleTextForBreedcrumbs: {
    type: String,
    default: 'Dashboard'
  }
})
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset>
      <header class="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
        <div class="flex items-center gap-2 px-4">
          <SidebarTrigger class="-ml-1" />
          <Separator orientation="vertical" class="mr-2 h-4" />

          <Breadcrumb>
            <BreadcrumbList>
              
              <BreadcrumbItem class="hidden md:block">
                <BreadcrumbLink href="#">
                  Welcome! {{ user.name }} <!-- then call it here. -->
                </BreadcrumbLink>
              </BreadcrumbItem>

              <BreadcrumbSeparator class="hidden md:block" />

              <BreadcrumbItem>
                <BreadcrumbPage>{{ props.pageTitleTextForBreedcrumbs }}</BreadcrumbPage> <!--  if ag page way title then automatically use the 'Dashboard' kay maomay naka default sa props nimo sa babaw na script -->
              </BreadcrumbItem>

            </BreadcrumbList>
          </Breadcrumb>
        </div>
      </header>
      <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
        <slot />
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>
