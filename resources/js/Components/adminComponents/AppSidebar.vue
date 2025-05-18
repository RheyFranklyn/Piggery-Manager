<script setup lang="ts">
import NavMain from '@/Components/adminComponents/NavMain.vue'
import NavProjects from '@/Components/adminComponents/NavProjects.vue'
import NavUser from '@/Components/adminComponents/NavUser.vue'
import BusinessNameDisplay from '@/Components/adminComponents/BusinessNameDisplay.vue'

import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from '@/Components/ui/sidebar'

import {
  AudioWaveform,
  BookOpen,
  Bot,
  Command,
  Frame,
  GalleryVerticalEnd,
  Map,
  PieChart,
  Settings2,
  SquareTerminal,
} from 'lucide-vue-next'

const data = {
  navMain: [
    {
      title: 'Pigs Management',
      url: '#',
      icon: SquareTerminal,
      isActive: true,
      items: [
        { title: 'All Pigs', url: route('Admin-Pigs.index') },
        { title: 'Weight Logs', url: '#' },
        { title: 'Health & Treatments', url: '#' },
      ],
    },
    {
      title: 'Pens & Housing',
      url: '#',
      icon: Bot,
      isActive: true,
      items: [
        { title: 'All Pens', url: '#' },
        { title: 'Pen Status', url: '#' },
      ],
    },
    {
      title: 'Inventory',
      url: '#',
      icon: BookOpen,
      isActive: true,
      items: [
        { title: 'Feed Stock', url: '#' },
        { title: 'Vitamins & Medicine', url: '#' },
      ],
    },
    {
      title: 'Expenses & Sales',
      url: '#',
      icon:  PieChart,
      items: [
        { title: 'Expense', url: route('Admin-Expenses.index') },
      ],
    },
  ],
  projects: [
    { name: 'Planning', url: '#', icon: Frame },
    { name: 'Sales & Marketing', url: '#', icon: Settings2 },
    { name: 'Travel', url: '#', icon: Map },
  ],
}
</script>

<template>
  <Sidebar>
    <SidebarHeader>
      <BusinessNameDisplay />
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="data.navMain" />
      <NavProjects :projects="data.projects" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
