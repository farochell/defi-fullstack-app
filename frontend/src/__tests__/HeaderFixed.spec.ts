import { describe, it, expect, vi } from "vitest"
import { mount } from "@vue/test-utils"
import HeaderFixed from "../components/HeaderFixed.vue"

describe("HeaderFixed", () => {
  it("affiche le composant", () => {
    const wrapper = mount(HeaderFixed)
    expect(wrapper.find("header").exists()).toBe(true)
  })

  it("affiche le titre lorsqu'il est fourni", () => {
    const wrapper = mount(HeaderFixed, {
      props: {
        title: "Defi fullstack"
      }
    })
    expect(wrapper.text()).toContain("Defi fullstack")
  })

  it("n'affiche pas le titre lorsqu'il n'est pas fourni", () => {
    const wrapper = mount(HeaderFixed)
    expect(wrapper.find("span").text()).toBe("")
  })

  it("affiche le logo lorsque logoSrc est fourni", () => {
    const wrapper = mount(HeaderFixed, {
      props: {
        logoSrc: "/logo.svg"
      }
    })
    const img = wrapper.find("img")
    expect(img.exists()).toBe(true)
    expect(img.attributes("src")).toBe("/logo.svg")
    expect(img.attributes("alt")).toBe("Logo")
  })

  it("n'affiche pas le logo lorsque logoSrc n'est pas fourni", () => {
    const wrapper = mount(HeaderFixed)
    expect(wrapper.find("img").exists()).toBe(false)
  })

  it("affiche le bouton de connexion lorsque showLogin est true", () => {
    const wrapper = mount(HeaderFixed, {
      props: {
        showLogin: true
      }
    })
    const button = wrapper.find("button")
    expect(button.exists()).toBe(true)
    expect(button.text()).toBe("Se connecter")
  })

  it("n'affiche pas le bouton de connexion lorsque showLogin est false", () => {
    const wrapper = mount(HeaderFixed, {
      props: {
        showLogin: false
      },
      global: {
        stubs: {
          RouterLink: true
        },
        mocks: {
          $router: {
            push: vi.fn()
          }
        }
      }
    })
    const loginButton = wrapper.findAll("button").find(btn => btn.text() === "Se connecter")
    expect(loginButton).toBeUndefined()
    // Doit afficher les boutons tableau de bord et déconnexion à la place
    expect(wrapper.findAll("button").length).toBeGreaterThan(0)
  })

  it("n'affiche pas le bouton de connexion par défaut", () => {
    const wrapper = mount(HeaderFixed, {
      global: {
        stubs: {
          RouterLink: true
        },
        mocks: {
          $router: {
            push: vi.fn()
          }
        }
      }
    })
    const loginButton = wrapper.findAll("button").find(btn => btn.text() === "Se connecter")
    expect(loginButton).toBeUndefined()
  })

  it("affiche toutes les props ensemble", () => {
    const wrapper = mount(HeaderFixed, {
      props: {
        title: "Test App",
        logoSrc: "/test-logo.svg",
        showLogin: true
      }
    })
    expect(wrapper.text()).toContain("Test App")
    expect(wrapper.find("img").attributes("src")).toBe("/test-logo.svg")
    expect(wrapper.find("button").exists()).toBe(true)
  })

  it("affiche le contenu du slot center", () => {
    const wrapper = mount(HeaderFixed, {
      slots: {
        center: "<nav>Navigation</nav>"
      }
    })
    expect(wrapper.html()).toContain("Navigation")
  })

  it("possède les classes CSS appropriées pour le positionnement fixe", () => {
    const wrapper = mount(HeaderFixed)
    const header = wrapper.find("header")
    expect(header.classes()).toContain("fixed")
    expect(header.classes()).toContain("top-0")
    expect(header.classes()).toContain("left-0")
    expect(header.classes()).toContain("w-full")
  })

  it("émet l'événement login lorsque le bouton de connexion est cliqué", async () => {
    const wrapper = mount(HeaderFixed, {
      props: {
        showLogin: true
      }
    })
    const button = wrapper.find("button")
    await button.trigger("click")
    expect(wrapper.emitted()).toHaveProperty("login")
    expect(wrapper.emitted("login")).toHaveLength(1)
  })
})
