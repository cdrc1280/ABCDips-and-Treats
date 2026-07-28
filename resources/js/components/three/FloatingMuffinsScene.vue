<template>
  <canvas ref="canvas" class="fixed inset-0 w-full h-full pointer-events-none" style="z-index: 0;" />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import * as THREE from 'three'

const canvas = ref(null)
let renderer, scene, camera, animFrameId
let muffins = []
let particles
let mouseX = 0, mouseY = 0
let scrollRatio = 0

function createMuffin(x, y, z, scale = 1) {
  const group = new THREE.Group()
  
  // Muffin base (cup)
  const baseGeo = new THREE.CylinderGeometry(0.3 * scale, 0.25 * scale, 0.4 * scale, 16)
  const baseMat = new THREE.MeshLambertMaterial({ color: new THREE.Color('#C08E5D') })
  const base = new THREE.Mesh(baseGeo, baseMat)
  base.position.y = -0.1 * scale
  group.add(base)
  
  // Frosting dome
  const frostGeo = new THREE.SphereGeometry(0.32 * scale, 16, 12, 0, Math.PI * 2, 0, Math.PI * 0.6)
  const colors = ['#5C3A22', '#D9A876', '#C08E5D', '#FBF3E7', '#8C7A68']
  const frostMat = new THREE.MeshLambertMaterial({ color: new THREE.Color(colors[Math.floor(Math.random() * colors.length)]) })
  const frost = new THREE.Mesh(frostGeo, frostMat)
  frost.position.y = 0.18 * scale
  group.add(frost)
  
  group.position.set(x, y, z)
  group.userData = {
    originalY: y,
    originalX: x,
    rotSpeed: (Math.random() - 0.5) * 0.008,
    floatOffset: Math.random() * Math.PI * 2,
    floatSpeed: 0.3 + Math.random() * 0.3,
    parallaxX: (Math.random() - 0.5) * 4,
    parallaxY: (Math.random() - 0.5) * 3,
  }
  return group
}

function createParticles() {
  const geo = new THREE.BufferGeometry()
  const count = 200
  const positions = new Float32Array(count * 3)
  for (let i = 0; i < count; i++) {
    positions[i * 3] = (Math.random() - 0.5) * 20
    positions[i * 3 + 1] = (Math.random() - 0.5) * 20
    positions[i * 3 + 2] = (Math.random() - 0.5) * 5
  }
  geo.setAttribute('position', new THREE.BufferAttribute(positions, 3))
  const mat = new THREE.PointsMaterial({ color: 0xFBF3E7, size: 0.04, transparent: true, opacity: 0.5 })
  return new THREE.Points(geo, mat)
}

onMounted(() => {
  const w = window.innerWidth
  const h = window.innerHeight

  renderer = new THREE.WebGLRenderer({ canvas: canvas.value, alpha: true, antialias: true })
  renderer.setSize(w, h)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))

  scene = new THREE.Scene()
  camera = new THREE.PerspectiveCamera(60, w / h, 0.1, 100)
  camera.position.z = 8

  // Lights
  const ambient = new THREE.AmbientLight(0xFBF3E7, 0.8)
  scene.add(ambient)
  const dirLight = new THREE.DirectionalLight(0xD9A876, 1.2)
  dirLight.position.set(3, 5, 3)
  scene.add(dirLight)

  // Spawn muffins
  const positions = [
    [-4, 2, -1], [4, 3, -2], [-3, -2, 0], [5, -1, -3],
    [0, 4, -2], [-5, 0, -1], [3, -3, -1], [6, 2, -3],
    [-2, -4, 0], [2, 1, -4], [-6, 3, -2], [1, -2, -1]
  ]
  positions.forEach(([x, y, z]) => {
    const s = 0.5 + Math.random() * 0.7
    const m = createMuffin(x, y, z, s)
    scene.add(m)
    muffins.push(m)
  })

  particles = createParticles()
  scene.add(particles)

  // Events
  const onMouse = (e) => {
    mouseX = (e.clientX / w - 0.5) * 2
    mouseY = -(e.clientY / h - 0.5) * 2
  }
  const onScroll = () => {
    const docH = document.body.scrollHeight - window.innerHeight
    scrollRatio = docH > 0 ? window.scrollY / docH : 0
  }
  const onResize = () => {
    const nw = window.innerWidth, nh = window.innerHeight
    camera.aspect = nw / nh
    camera.updateProjectionMatrix()
    renderer.setSize(nw, nh)
  }
  window.addEventListener('mousemove', onMouse)
  window.addEventListener('scroll', onScroll, { passive: true })
  window.addEventListener('resize', onResize)

  const clock = new THREE.Clock()
  function animate() {
    animFrameId = requestAnimationFrame(animate)
    const t = clock.getElapsedTime()

    muffins.forEach((m, i) => {
      const { originalY, originalX, rotSpeed, floatOffset, floatSpeed, parallaxX, parallaxY } = m.userData
      // Float up and down
      m.position.y = originalY + Math.sin(t * floatSpeed + floatOffset) * 0.3
      // Scroll-driven drift
      m.position.x = originalX + scrollRatio * parallaxX
      m.position.y += scrollRatio * parallaxY
      // Slow rotation
      m.rotation.y += rotSpeed
      m.rotation.z = Math.sin(t * 0.2 + i) * 0.05
    })

    // Camera mouse parallax
    camera.position.x += (mouseX * 0.5 - camera.position.x) * 0.05
    camera.position.y += (mouseY * 0.3 - camera.position.y) * 0.05
    camera.lookAt(0, 0, 0)

    // Drift particles upward
    const pos = particles.geometry.attributes.position
    for (let i = 0; i < pos.count; i++) {
      pos.setY(i, pos.getY(i) + 0.005)
      if (pos.getY(i) > 10) pos.setY(i, -10)
    }
    pos.needsUpdate = true

    renderer.render(scene, camera)
  }
  animate()

  return () => {
    window.removeEventListener('mousemove', onMouse)
    window.removeEventListener('scroll', onScroll)
    window.removeEventListener('resize', onResize)
  }
})

onUnmounted(() => {
  cancelAnimationFrame(animFrameId)
  renderer?.dispose()
})
</script>
