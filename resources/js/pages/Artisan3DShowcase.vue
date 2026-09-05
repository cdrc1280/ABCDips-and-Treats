<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import * as THREE from 'three'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { Cake, ShoppingBag, Flame, Sparkles, Award, ArrowRight, Eye, ChevronRight, Box, Cookie } from 'lucide-vue-next'

gsap.registerPlugin(ScrollTrigger)

const canvasRef = ref<HTMLCanvasElement | null>(null)
const cardRefs = ref<HTMLElement[]>([])
const activeFlavor = ref<number>(0)

let scene: THREE.Scene
let camera: THREE.PerspectiveCamera
let renderer: THREE.WebGLRenderer
let pastryGroup: THREE.Group
let loafMesh: THREE.Mesh
let loafMat: THREE.MeshPhysicalMaterial
let chocGroup: THREE.Group
let almondGroup: THREE.Group
let particleSystem: THREE.Points
let animationFrameId: number
const clock = new THREE.Clock()

let mouseX = 0
let mouseY = 0
let targetX = 0
let targetY = 0

function handleMouseMove(e: MouseEvent) {
  mouseX = (e.clientX / window.innerWidth - 0.5) * 2
  mouseY = -(e.clientY / window.innerHeight - 0.5) * 2
}

function handleResize() {
  if (!camera || !renderer) return
  camera.aspect = window.innerWidth / window.innerHeight
  camera.updateProjectionMatrix()
  renderer.setSize(window.innerWidth, window.innerHeight)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
}

function animate() {
  const t = clock.getElapsedTime()

  targetX += (mouseX - targetX) * 0.04
  targetY += (mouseY - targetY) * 0.04

  if (pastryGroup) {
    pastryGroup.rotation.x = 0.25 + targetY * 0.4 + Math.sin(t * 0.8) * 0.08
    pastryGroup.rotation.y = t * 0.25 + targetX * 0.6
    pastryGroup.position.y = Math.sin(t * 1.2) * 0.15
  }

  if (almondGroup) {
    almondGroup.children.forEach((a, idx) => {
      a.rotation.x += 0.01 * (idx % 2 === 0 ? 1 : -1)
      a.rotation.z += 0.008
    })
  }

  if (chocGroup) {
    chocGroup.children.forEach((c, idx) => {
      c.rotation.y += 0.012 * (idx % 2 === 0 ? -1 : 1)
    })
  }

  if (particleSystem) {
    particleSystem.rotation.y = t * 0.03
  }

  camera.position.x = targetX * 0.6
  camera.position.y = targetY * 0.4
  camera.lookAt(scene.position)

  renderer.render(scene, camera)
  animationFrameId = requestAnimationFrame(animate)
}

function switchFlavor(flavorIndex: number) {
  activeFlavor.value = flavorIndex

  if (!loafMat || !almondGroup || !chocGroup) return

  if (flavorIndex === 0) {
    gsap.to(loafMat.color, { r: 0.85, g: 0.55, b: 0.28, duration: 1 })
    gsap.to(almondGroup.scale, { x: 1.5, y: 1.5, z: 1.5, duration: 0.8, ease: 'back.out(1.7)' })
    gsap.to(chocGroup.scale, { x: 0.6, y: 0.6, z: 0.6, duration: 0.8 })
  } else if (flavorIndex === 1) {
    gsap.to(loafMat.color, { r: 0.50, g: 0.25, b: 0.15, duration: 1 })
    gsap.to(chocGroup.scale, { x: 1.6, y: 1.6, z: 1.6, duration: 0.8, ease: 'back.out(1.7)' })
    gsap.to(almondGroup.scale, { x: 0.5, y: 0.5, z: 0.5, duration: 0.8 })
  } else if (flavorIndex === 2) {
    gsap.to(loafMat.color, { r: 0.78, g: 0.42, b: 0.22, duration: 1 })
    gsap.to(almondGroup.scale, { x: 1, y: 1, z: 1, duration: 0.8 })
    gsap.to(chocGroup.scale, { x: 1, y: 1, z: 1, duration: 0.8 })
  } else {
    gsap.to(loafMat.color, { r: 0.75, g: 0.37, b: 0.20, duration: 1 })
    gsap.to(almondGroup.scale, { x: 1.2, y: 1.2, z: 1.2, duration: 0.8 })
    gsap.to(chocGroup.scale, { x: 1.2, y: 1.2, z: 1.2, duration: 0.8 })
  }
}

function initCardTilt(el: HTMLElement) {
  el.addEventListener('mousemove', (e) => {
    const rect = el.getBoundingClientRect()
    const x = e.clientX - rect.left - rect.width / 2
    const y = e.clientY - rect.top - rect.height / 2
    const rotateX = -(y / (rect.height / 2)) * 14
    const rotateY = (x / (rect.width / 2)) * 14

    gsap.to(el, {
      rotateX,
      rotateY,
      transformPerspective: 1000,
      ease: 'power2.out',
      duration: 0.35,
    })
  })

  el.addEventListener('mouseleave', () => {
    gsap.to(el, {
      rotateX: 0,
      rotateY: 0,
      ease: 'power3.out',
      duration: 0.8,
    })
  })
}

onMounted(() => {
  if (!canvasRef.value) return

  // Three.js Scene Setup
  scene = new THREE.Scene()
  camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000)
  camera.position.set(0, 0, 8)

  renderer = new THREE.WebGLRenderer({
    canvas: canvasRef.value,
    alpha: true,
    antialias: true,
  })
  renderer.setSize(window.innerWidth, window.innerHeight)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  renderer.toneMapping = THREE.ACESFilmicToneMapping
  renderer.toneMappingExposure = 1.2

  // Lighting Rig
  const ambientLight = new THREE.AmbientLight(0xffeedd, 1.2)
  scene.add(ambientLight)

  const keyLight = new THREE.DirectionalLight(0xf59e0b, 3.5)
  keyLight.position.set(5, 6, 4)
  scene.add(keyLight)

  const rimLight = new THREE.PointLight(0xd97706, 4, 12)
  rimLight.position.set(-5, -3, 2)
  scene.add(rimLight)

  const topLight = new THREE.PointLight(0xfff7ed, 2, 8)
  topLight.position.set(0, 4, 3)
  scene.add(topLight)

  // 3D Master Group
  pastryGroup = new THREE.Group()
  scene.add(pastryGroup)

  // Loaf Geometry & Material
  const loafGeo = new THREE.BoxGeometry(3.2, 1.4, 1.4, 32, 16, 16)
  const pos = loafGeo.attributes.position
  for (let i = 0; i < pos.count; i++) {
    let x = pos.getX(i)
    let y = pos.getY(i)
    let z = pos.getZ(i)
    if (y > 0) y += Math.sin(x * 1.5) * 0.18 + Math.cos(z * 2.0) * 0.12
    pos.setXYZ(i, x, y, z)
  }
  loafGeo.computeVertexNormals()

  loafMat = new THREE.MeshPhysicalMaterial({
    color: 0xc05f34,
    emissive: 0x3d170a,
    roughness: 0.75,
    metalness: 0.05,
    clearcoat: 0.35,
    clearcoatRoughness: 0.4,
    reflectivity: 0.4,
  })
  loafMesh = new THREE.Mesh(loafGeo, loafMat)
  pastryGroup.add(loafMesh)

  // Floating Chocolate Chunks
  chocGroup = new THREE.Group()
  const chocGeo = new THREE.DodecahedronGeometry(0.22, 0)
  const chocMat = new THREE.MeshPhysicalMaterial({
    color: 0x1f1008,
    roughness: 0.25,
    metalness: 0.1,
    clearcoat: 0.8,
  })
  const chocPositions = [
    [-1.1, 0.8, 0.3], [-0.5, 0.9, -0.2], [0.3, 0.85, 0.4],
    [1.0, 0.82, -0.3], [-1.8, 0.4, 0.8], [1.9, -0.3, 0.9],
    [-0.8, -1.2, 1.1], [1.2, 1.4, 0.6], [-2.2, 1.2, -0.5]
  ]
  chocPositions.forEach((p) => {
    const cMesh = new THREE.Mesh(chocGeo, chocMat)
    cMesh.position.set(p[0], p[1], p[2])
    cMesh.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, 0)
    chocGroup.add(cMesh)
  })
  pastryGroup.add(chocGroup)

  // Floating Almond Slivers
  almondGroup = new THREE.Group()
  const almondGeo = new THREE.CylinderGeometry(0.18, 0.18, 0.03, 16)
  almondGeo.scale(1.8, 1, 0.8)
  const almondMat = new THREE.MeshPhysicalMaterial({
    color: 0xfef08a,
    emissive: 0x78350f,
    roughness: 0.4,
    metalness: 0.0,
    clearcoat: 0.5,
  })
  const almondPositions = [
    [-1.3, 0.9, -0.3], [-0.8, 0.92, 0.4], [0.0, 0.95, -0.1],
    [0.7, 0.9, 0.35], [1.3, 0.85, -0.2], [-1.5, 1.3, 0.7],
    [1.6, 1.1, 0.8], [-0.4, -1.1, 1.0], [2.1, -0.8, 0.5]
  ]
  almondPositions.forEach((p) => {
    const aMesh = new THREE.Mesh(almondGeo, almondMat)
    aMesh.position.set(p[0], p[1], p[2])
    aMesh.rotation.set(Math.random() * 0.8, Math.random() * Math.PI, Math.random() * 0.5)
    almondGroup.add(aMesh)
  })
  pastryGroup.add(almondGroup)

  // Golden Sugar Sparkles
  const particleCount = 400
  const particlePositions = new Float32Array(particleCount * 3)
  for (let i = 0; i < particleCount * 3; i += 3) {
    particlePositions[i] = (Math.random() - 0.5) * 14
    particlePositions[i + 1] = (Math.random() - 0.5) * 10
    particlePositions[i + 2] = (Math.random() - 0.5) * 8
  }
  const particleGeo = new THREE.BufferGeometry()
  particleGeo.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3))
  const particleMat = new THREE.PointsMaterial({
    color: 0xfde68a,
    size: 0.035,
    transparent: true,
    opacity: 0.75,
    blending: THREE.AdditiveBlending,
  })
  particleSystem = new THREE.Points(particleGeo, particleMat)
  scene.add(particleSystem)

  // Mouse Listeners
  window.addEventListener('mousemove', handleMouseMove)
  window.addEventListener('resize', handleResize)

  animate()

  // Init Card 3D Tilt
  cardRefs.value.forEach((card) => {
    if (card) initCardTilt(card)
  })

  // GSAP Entrance
  gsap.from('.hero-content > *', {
    opacity: 0,
    y: 40,
    stagger: 0.15,
    duration: 1.2,
    ease: 'power4.out',
    delay: 0.2,
  })
})

onBeforeUnmount(() => {
  cancelAnimationFrame(animationFrameId)
  window.removeEventListener('mousemove', handleMouseMove)
  window.removeEventListener('resize', handleResize)
  if (renderer) renderer.dispose()
})
</script>

<template>
  <div class="relative min-h-screen bg-[#0c0704] text-[#faf5f0] overflow-hidden font-sans">
    <!-- WebGL Canvas Layer -->
    <canvas ref="canvasRef" class="fixed inset-0 w-full h-full pointer-events-none z-0" />

    <!-- Ambient Glow -->
    <div class="fixed inset-0 bg-[radial-gradient(circle_at_50%_35%,rgba(208,119,66,0.18)_0%,rgba(105,51,36,0.08)_45%,transparent_70%)] pointer-events-none z-0" />

    <!-- Main Content -->
    <div class="relative z-10 flex flex-col min-h-screen max-w-7xl mx-auto px-6 py-8">
      
      <!-- Nav -->
      <header class="flex items-center justify-between mb-12">
        <div class="flex items-center space-x-3">
          <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-amber-400 flex items-center justify-center shadow-lg shadow-amber-600/30">
            <Cake class="w-5 h-5 text-white" />
          </div>
          <div>
            <span class="text-xl font-extrabold font-serif bg-gradient-to-r from-amber-100 to-amber-400 bg-clip-text text-transparent">ABCDips</span>
            <span class="text-xs block tracking-widest text-amber-300/70 font-semibold uppercase">Artisan Bakery</span>
          </div>
        </div>
        <button class="px-6 py-2.5 rounded-full text-sm font-bold bg-gradient-to-r from-amber-600 to-amber-500 text-white shadow-lg shadow-amber-600/30 hover:scale-105 active:scale-95 transition-transform">
          Order Box Now
        </button>
      </header>

      <!-- Hero Content -->
      <main class="hero-content flex-1 flex flex-col items-center text-center justify-center pt-8 pb-20">
        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-[#180c08]/80 border border-amber-600/30 text-xs font-semibold text-amber-300 mb-6">
          <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping" />
          <span>Fresh Batch Baked Daily · 100% Real Butter &amp; Pure Vanilla</span>
        </div>

        <h1 class="font-serif text-5xl sm:text-7xl lg:text-8xl font-bold tracking-tight mb-6">
          Baked with Soul. <br />
          <span class="bg-gradient-to-r from-amber-200 via-amber-300 to-amber-500 bg-clip-text text-transparent italic font-normal">Sliced to Perfection.</span>
        </h1>

        <p class="text-lg sm:text-xl text-amber-100/80 max-w-2xl mb-10 font-light">
          Experience our signature multi-flavor banana bread sampler. Handcrafted with crunchy toasted almonds, Belgian dark chocolate chunks, and cinnamon streusel crust.
        </p>

        <!-- Flavor Switcher -->
        <div class="flex flex-wrap items-center justify-center gap-3 mb-10 p-2 rounded-2xl bg-[#1c120c]/60 border border-white/10 backdrop-blur-md">
          <button
            v-for="(flavor, idx) in ['🌰 Toasted Almond', '🍫 Belgian Dark Cacao', '✨ Cinnamon Streusel', '🎁 Full Sampler Box']"
            :key="idx"
            @click="switchFlavor(idx)"
            :class="[
              'px-5 py-2.5 rounded-xl text-xs font-bold tracking-wide uppercase transition-all',
              activeFlavor === idx ? 'bg-amber-600 text-white shadow-md' : 'text-amber-200/70 hover:text-white hover:bg-white/5'
            ]"
          >
            {{ flavor }}
          </button>
        </div>

        <!-- 3D Tilt Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full mt-16 text-left">
          <div
            v-for="(card, i) in [
              { icon: Flame, badge: 'Top Rated', price: '₱380', title: 'Toasted Almond Flake', desc: 'Crown of crispy golden slivered almonds layered over caramelized banana bread.' },
              { icon: Sparkles, badge: 'Bestseller', price: '₱420', title: 'Belgian Dark Cacao', desc: 'Loaded with 70% dark Belgian cacao chunks that melt into warm velvety pockets.' },
              { icon: Award, badge: 'Chef Choice', price: '₱390', title: 'Cinnamon Streusel', desc: 'Brown sugar crumb topping infused with Ceylon cinnamon for crunchy texture.' }
            ]"
            :key="i"
            :ref="el => { if (el) cardRefs[i] = el as HTMLElement }"
            class="p-8 rounded-3xl bg-[#180e0a]/70 backdrop-blur-xl border border-amber-200/10 shadow-2xl hover:border-amber-500/40 transition-colors"
          >
            <div class="flex items-center justify-between mb-6">
              <span class="px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-[11px] font-bold text-amber-300 uppercase">{{ card.badge }}</span>
              <span class="text-xl font-bold font-serif text-amber-200">{{ card.price }}</span>
            </div>
            <h3 class="font-serif text-2xl font-bold mb-2 text-white">{{ card.title }}</h3>
            <p class="text-sm text-amber-100/70 leading-relaxed font-light">{{ card.desc }}</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
