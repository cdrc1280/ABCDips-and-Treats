<template>
  <div ref="canvasContainer" class="absolute inset-0 w-full h-full overflow-hidden pointer-events-none z-0 opacity-80"></div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import * as THREE from 'three'

const canvasContainer = ref(null)
let renderer, scene, camera, animationFrameId
let meshes = []
let particles

onMounted(() => {
  if (!canvasContainer.value) return

  const width = canvasContainer.value.clientWidth || window.innerWidth
  const height = canvasContainer.value.clientHeight || 500

  // 1. Scene setup
  scene = new THREE.Scene()

  // 2. Camera setup
  camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000)
  camera.position.z = 15

  // 3. Renderer setup
  renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true })
  renderer.setSize(width, height)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  canvasContainer.value.appendChild(renderer.domElement)

  // 4. Lighting setup
  const ambientLight = new THREE.AmbientLight(0xfff5e6, 1.2)
  scene.add(ambientLight)

  const dirLight = new THREE.DirectionalLight(0xd9a876, 2.5)
  dirLight.position.set(5, 10, 7)
  scene.add(dirLight)

  const pointLight = new THREE.PointLight(0xc08e5d, 2, 20)
  pointLight.position.set(-5, -5, 5)
  scene.add(pointLight)

  // 5. Floating Pastry Geometries (Warm Bakery Colors)
  const materials = [
    new THREE.MeshStandardMaterial({ color: 0xD9A876, roughness: 0.3, metalness: 0.1 }),
    new THREE.MeshStandardMaterial({ color: 0x5C3A22, roughness: 0.4, metalness: 0.2 }),
    new THREE.MeshStandardMaterial({ color: 0xC08E5D, roughness: 0.2, metalness: 0.1 }),
  ]

  // Floating Cookie Disc 1
  const geo1 = new THREE.CylinderGeometry(1.8, 1.8, 0.4, 32)
  const mesh1 = new THREE.Mesh(geo1, materials[0])
  mesh1.position.set(-5, 2, -2)
  mesh1.rotation.x = 0.5
  scene.add(mesh1)
  meshes.push(mesh1)

  // Floating Donut Torus 2
  const geo2 = new THREE.TorusGeometry(1.5, 0.6, 16, 32)
  const mesh2 = new THREE.Mesh(geo2, materials[1])
  mesh2.position.set(6, -1, -3)
  mesh2.rotation.y = 0.8
  scene.add(mesh2)
  meshes.push(mesh2)

  // Floating Cinnamon Roll Sphere 3
  const geo3 = new THREE.IcosahedronGeometry(1.4, 2)
  const mesh3 = new THREE.Mesh(geo3, materials[2])
  mesh3.position.set(1, 3, -4)
  scene.add(mesh3)
  meshes.push(mesh3)

  // 6. Flour Particles Cloud
  const particleGeo = new THREE.BufferGeometry()
  const particleCount = 120
  const posArray = new Float32Array(particleCount * 3)

  for (let i = 0; i < particleCount * 3; i++) {
    posArray[i] = (Math.random() - 0.5) * 25
  }

  particleGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3))
  const particleMat = new THREE.PointsMaterial({
    size: 0.08,
    color: 0xFBF3E7,
    transparent: true,
    opacity: 0.6
  })
  particles = new THREE.Points(particleGeo, particleMat)
  scene.add(particles)

  // 7. Mouse Parallax Effect
  let mouseX = 0, mouseY = 0
  const handleMouseMove = (e) => {
    mouseX = (e.clientX / window.innerWidth - 0.5) * 2
    mouseY = (e.clientY / window.innerHeight - 0.5) * 2
  }
  window.addEventListener('mousemove', handleMouseMove)

  // 8. Animation Loop
  const animate = () => {
    animationFrameId = requestAnimationFrame(animate)

    meshes.forEach((mesh, idx) => {
      mesh.rotation.x += 0.005 * (idx + 1)
      mesh.rotation.y += 0.008 * (idx + 1)
      mesh.position.y += Math.sin(Date.now() * 0.001 + idx) * 0.003
    })

    if (particles) {
      particles.rotation.y += 0.0005
    }

    // Parallax easing
    camera.position.x += (mouseX * 1.5 - camera.position.x) * 0.05
    camera.position.y += (-mouseY * 1.5 - camera.position.y) * 0.05
    camera.lookAt(scene.position)

    renderer.render(scene, camera)
  }

  animate()

  // Resize handler
  const handleResize = () => {
    if (!canvasContainer.value) return
    const w = canvasContainer.value.clientWidth || window.innerWidth
    const h = canvasContainer.value.clientHeight || 500
    camera.aspect = w / h
    camera.updateProjectionMatrix()
    renderer.setSize(w, h)
  }
  window.addEventListener('resize', handleResize)

  onBeforeUnmount(() => {
    window.removeEventListener('mousemove', handleMouseMove)
    window.removeEventListener('resize', handleResize)
    if (animationFrameId) cancelAnimationFrame(animationFrameId)
    if (renderer) renderer.dispose()
  })
})
</script>
