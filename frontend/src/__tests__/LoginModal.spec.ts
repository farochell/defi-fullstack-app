import { describe, it, expect, vi, beforeEach } from "vitest"
import { mount, flushPromises } from "@vue/test-utils"
import LoginModal from "../components/LoginModal.vue"
import { apiService } from "@/services/api"

// Mock the API service
vi.mock("@/services/api", () => ({
  apiService: {
    login: vi.fn(),
  },
}))

// Helper to get teleported content
const findInDocument = (selector: string) => document.body.querySelector(selector)

describe("LoginModal", () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
  })

  it("affiche le modal lorsque show est true", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()
    expect(document.body.textContent).toContain("Connexion")
    wrapper.unmount()
  })

  it("n'affiche pas le modal lorsque show est false", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: false },
      attachTo: document.body,
    })
    await flushPromises()
    expect(findInDocument(".fixed")).toBeNull()
    wrapper.unmount()
  })

  it("possède des champs email et mot de passe", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()
    expect(findInDocument("input[type='email']")).toBeTruthy()
    expect(findInDocument("input[type='password']")).toBeTruthy()
    wrapper.unmount()
  })

  it("affiche une erreur de validation lorsque les champs sont vides", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const form = findInDocument("form") as HTMLFormElement
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }))
    await flushPromises()

    expect(document.body.textContent).toContain("Veuillez remplir tous les champs")
    wrapper.unmount()
  })

  it("appelle apiService.login lorsque le formulaire est soumis avec des données valides", async () => {
    vi.mocked(apiService.login).mockResolvedValue({
      data: { token: "test-token" },
    })

    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const emailInput = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInput = findInDocument("input[type='password']") as HTMLInputElement

    emailInput.value = "test@example.com"
    emailInput.dispatchEvent(new Event("input", { bubbles: true }))
    passwordInput.value = "password123"
    passwordInput.dispatchEvent(new Event("input", { bubbles: true }))
    await flushPromises()

    const form = findInDocument("form") as HTMLFormElement
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }))
    await flushPromises()

    expect(apiService.login).toHaveBeenCalledWith({
      email: "test@example.com",
      password: "password123",
    })
    wrapper.unmount()
  })

  it("émet l'événement success lors d'une connexion réussie", async () => {
    vi.mocked(apiService.login).mockResolvedValue({
      data: { token: "test-token" },
    })

    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const emailInput = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInput = findInDocument("input[type='password']") as HTMLInputElement

    emailInput.value = "test@example.com"
    emailInput.dispatchEvent(new Event("input", { bubbles: true }))
    passwordInput.value = "password123"
    passwordInput.dispatchEvent(new Event("input", { bubbles: true }))
    await flushPromises()

    const form = findInDocument("form") as HTMLFormElement
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }))
    await flushPromises()

    expect(wrapper.emitted()).toHaveProperty("success")
    wrapper.unmount()
  })

  it("affiche un message d'erreur lors d'un échec de connexion", async () => {
    vi.mocked(apiService.login).mockResolvedValue({
      error: { code: "UNAUTHORIZED", message: "Email ou mot de passe incorrect" },
    })

    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const emailInput = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInput = findInDocument("input[type='password']") as HTMLInputElement

    emailInput.value = "test@example.com"
    emailInput.dispatchEvent(new Event("input", { bubbles: true }))
    passwordInput.value = "wrongpassword"
    passwordInput.dispatchEvent(new Event("input", { bubbles: true }))
    await flushPromises()

    const form = findInDocument("form") as HTMLFormElement
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }))
    await flushPromises()

    expect(document.body.textContent).toContain("Email ou mot de passe incorrect")
    wrapper.unmount()
  })

  it("émet l'événement close lorsque l'arrière-plan est cliqué", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const backdrop = findInDocument(".absolute.inset-0.bg-black\\/50") as HTMLElement
    backdrop.click()
    await flushPromises()

    expect(wrapper.emitted()).toHaveProperty("close")
    wrapper.unmount()
  })

  it("émet l'événement close lorsque le bouton de fermeture est cliqué", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const closeButton = findInDocument("button[aria-label='Fermer']") as HTMLButtonElement
    closeButton.click()
    await flushPromises()

    expect(wrapper.emitted()).toHaveProperty("close")
    wrapper.unmount()
  })

  it("émet l'événement close lorsque le bouton annuler est cliqué", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const cancelButton = Array.from(document.body.querySelectorAll("button"))
      .find(btn => btn.textContent?.includes("Annuler"))

    expect(cancelButton).toBeDefined()
    cancelButton?.click()
    await flushPromises()

    expect(wrapper.emitted()).toHaveProperty("close")
    wrapper.unmount()
  })

  it("efface les données du formulaire lorsque le modal se ferme", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const emailInput = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInput = findInDocument("input[type='password']") as HTMLInputElement

    emailInput.value = "test@example.com"
    emailInput.dispatchEvent(new Event("input", { bubbles: true }))
    passwordInput.value = "password123"
    passwordInput.dispatchEvent(new Event("input", { bubbles: true }))
    await flushPromises()

    const closeButton = findInDocument("button[aria-label='Fermer']") as HTMLButtonElement
    closeButton.click()
    await flushPromises()

    // Réouvrir le modal
    await wrapper.setProps({ show: false })
    await flushPromises()
    await wrapper.setProps({ show: true })
    await flushPromises()

    const emailInputAfter = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInputAfter = findInDocument("input[type='password']") as HTMLInputElement

    expect(emailInputAfter.value).toBe('')
    expect(passwordInputAfter.value).toBe('')
    wrapper.unmount()
  })

  it("désactive les champs et boutons pendant le chargement", async () => {
    vi.mocked(apiService.login).mockImplementation(
      () => new Promise((resolve) => setTimeout(resolve, 1000))
    )

    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const emailInput = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInput = findInDocument("input[type='password']") as HTMLInputElement

    emailInput.value = "test@example.com"
    emailInput.dispatchEvent(new Event("input", { bubbles: true }))
    passwordInput.value = "password123"
    passwordInput.dispatchEvent(new Event("input", { bubbles: true }))
    await flushPromises()

    const form = findInDocument("form") as HTMLFormElement
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }))
    await flushPromises()

    expect((findInDocument("input[type='email']") as HTMLInputElement).disabled).toBe(true)
    expect((findInDocument("input[type='password']") as HTMLInputElement).disabled).toBe(true)

    wrapper.unmount()
  })

  it("affiche le texte de chargement sur le bouton de soumission pendant le chargement", async () => {
    vi.mocked(apiService.login).mockImplementation(
      () => new Promise((resolve) => setTimeout(resolve, 1000))
    )

    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const emailInput = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInput = findInDocument("input[type='password']") as HTMLInputElement

    emailInput.value = "test@example.com"
    emailInput.dispatchEvent(new Event("input", { bubbles: true }))
    passwordInput.value = "password123"
    passwordInput.dispatchEvent(new Event("input", { bubbles: true }))
    await flushPromises()

    const form = findInDocument("form") as HTMLFormElement
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }))
    await flushPromises()

    const submitButton = Array.from(document.body.querySelectorAll("button"))
      .find(btn => btn.textContent?.includes("Connexion"))
    expect(submitButton?.textContent).toBe("Connexion...")

    wrapper.unmount()
  })

  it("possède les attributs d'accessibilité appropriés", async () => {
    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    expect(document.body.innerHTML).toBeTruthy()
    const closeButton = findInDocument("button[aria-label='Fermer']")
    expect(closeButton).toBeTruthy()

    wrapper.unmount()
  })

  it("gère les erreurs inattendues avec élégance", async () => {
    vi.mocked(apiService.login).mockRejectedValue(new Error("Network error"))

    const wrapper = mount(LoginModal, {
      props: { show: true },
      attachTo: document.body,
    })
    await flushPromises()

    const emailInput = findInDocument("input[type='email']") as HTMLInputElement
    const passwordInput = findInDocument("input[type='password']") as HTMLInputElement

    emailInput.value = "test@example.com"
    emailInput.dispatchEvent(new Event("input", { bubbles: true }))
    passwordInput.value = "password123"
    passwordInput.dispatchEvent(new Event("input", { bubbles: true }))
    await flushPromises()

    const form = findInDocument("form") as HTMLFormElement
    form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }))
    await flushPromises()

    expect(document.body.textContent).toContain("Une erreur inattendue est survenue")
    wrapper.unmount()
  })
})
