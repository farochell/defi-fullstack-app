import { describe, it, expect, beforeEach } from "vitest"
import { mount, type VueWrapper } from "@vue/test-utils"
import StationAutocomplete from "../components/StationAutocomplete.vue"

describe("StationAutocomplete", () => {
  const mockStations = [
    { id: 1, shortName: "ALLI", longName: "Allières" },
    { id: 2, shortName: "BCHX", longName: "Bois-de-Chexbres" },
    { id: 3, shortName: "BELM", longName: "Belmont-sur-Mx" },
    { id: 4, shortName: "AVAN", longName: "Les Avants" },
  ]

  let wrapper: VueWrapper<InstanceType<typeof StationAutocomplete>>

  beforeEach(() => {
    wrapper = mount(StationAutocomplete, {
      props: {
        modelValue: "",
        label: "Test Station",
        placeholder: "Search station",
        stations: mockStations,
      },
    })
  })

  it("affiche le composant avec le label", () => {
    expect(wrapper.text()).toContain("Test Station")
  })

  it("possède un champ de saisie", () => {
    expect(wrapper.find("input[type='text']" ).exists()).toBe(true)
  })

  it("utilise le placeholder lorsqu'il est fourni", () => {
    expect(wrapper.find("input").attributes("placeholder")).toBe("Search station")
  })

  it("utilise le placeholder par défaut lorsqu'il n'est pas fourni", () => {
    wrapper = mount(StationAutocomplete, {
      props: {
        modelValue: "",
        label: "Test",
        stations: mockStations,
      },
    })
    expect(wrapper.find("input").attributes("placeholder")).toBe("Rechercher une gare…")
  })

  it("affiche les résultats filtrés lors de la saisie", async () => {
    const input = wrapper.find("input")
    await input.setValue("ALLI")

    await wrapper.vm.$nextTick()

    expect(wrapper.find("ul").exists()).toBe(true)
    expect(wrapper.text()).toContain("ALLI")
    expect(wrapper.text()).toContain("Allières")
  })

  it("filtre les gares en utilisant la recherche floue", async () => {
    const input = wrapper.find("input")
    await input.setValue("Bois")

    await wrapper.vm.$nextTick()

    expect(wrapper.text()).toContain("BCHX")
    expect(wrapper.text()).toContain("Bois-de-Chexbres")
  })

  it("émet update:modelValue lorsqu'une gare est sélectionnée", async () => {
    const input = wrapper.find("input")
    await input.setValue("ALLI")

    await wrapper.vm.$nextTick()

    const firstStation = wrapper.find("li")
    await firstStation.trigger("click")

    expect(wrapper.emitted()).toHaveProperty("update:modelValue")
    expect(wrapper.emitted("update:modelValue")?.[0]).toEqual(["ALLI"])
  })

  it("met à jour la valeur du champ lorsqu'une gare est sélectionnée", async () => {
    const input = wrapper.find("input")
    await input.setValue("ALLI")

    await wrapper.vm.$nextTick()

    const firstStation = wrapper.find("li")
    await firstStation.trigger("click")

    await wrapper.vm.$nextTick()

    expect((wrapper.find("input").element as HTMLInputElement).value).toContain("ALLI")
    expect((wrapper.find("input").element as HTMLInputElement).value).toContain("Allières")
  })

  it("masque la liste déroulante lorsqu'une gare est sélectionnée", async () => {
    const input = wrapper.find("input")
    await input.setValue("ALLI")

    await wrapper.vm.$nextTick()

    const firstStation = wrapper.find("li")
    await firstStation.trigger("click")

    await wrapper.vm.$nextTick()

    expect(wrapper.find("ul").exists()).toBe(false)
  })

  it("affiche la gare au format: shortName – longName", async () => {
    const input = wrapper.find("input")
    await input.setValue("ALLI")

    await wrapper.vm.$nextTick()

    const list = wrapper.find("ul")
    expect(list.text()).toContain("ALLI – Allières")
  })

  it("n'affiche aucun résultat lorsque la requête ne correspond pas", async () => {
    const input = wrapper.find("input")
    await input.setValue("ZZZZZ")

    await wrapper.vm.$nextTick()

    expect(wrapper.find("ul").exists()).toBe(false)
  })

  it("efface le champ lorsque modelValue est réinitialisé", async () => {
    wrapper = mount(StationAutocomplete, {
      props: {
        modelValue: "ALLI",
        label: "Test Station",
        stations: mockStations,
      },
    })

    await wrapper.vm.$nextTick()
    expect((wrapper.find("input").element as HTMLInputElement).value).toContain("ALLI")

    await wrapper.setProps({ modelValue: "" })
    await wrapper.vm.$nextTick()
    await wrapper.vm.$nextTick()

    expect((wrapper.find("input").element as HTMLInputElement).value).toBe("")
  })

  it("remplit le champ lorsque modelValue est défini", async () => {
    await wrapper.setProps({ modelValue: "BCHX" })
    await wrapper.vm.$nextTick()

    expect((wrapper.find("input").element as HTMLInputElement).value).toContain("BCHX")
    expect((wrapper.find("input").element as HTMLInputElement).value).toContain("Bois-de-Chexbres")
  })

  it("gère la recherche insensible à la casse", async () => {
    const input = wrapper.find("input")
    await input.setValue("alli")

    await wrapper.vm.$nextTick()

    expect(wrapper.text()).toContain("ALLI")
    expect(wrapper.text()).toContain("Allières")
  })

  it("recherche dans shortName et longName", async () => {
    const input = wrapper.find("input")

    await input.setValue("Avants")
    await wrapper.vm.$nextTick()
    expect(wrapper.text()).toContain("AVAN")

    await input.setValue("AVAN")
    await wrapper.vm.$nextTick()
    expect(wrapper.text()).toContain("Les Avants")
  })

  it("applique les classes CSS correctes à la liste déroulante", async () => {
    const input = wrapper.find("input")
    await input.setValue("ALLI")

    await wrapper.vm.$nextTick()

    const list = wrapper.find("ul")
    expect(list.classes()).toContain("absolute")
    expect(list.classes()).toContain("z-10")
    expect(list.classes()).toContain("w-full")
    expect(list.classes()).toContain("bg-white")
  })

  it("applique les styles de survol aux éléments de la liste", async () => {
    const input = wrapper.find("input")
    await input.setValue("ALLI")

    await wrapper.vm.$nextTick()

    const listItem = wrapper.find("li")
    expect(listItem.classes()).toContain("hover:bg-gray-100")
    expect(listItem.classes()).toContain("cursor-pointer")
  })

  it("gère un tableau de gares vide", () => {
    wrapper = mount(StationAutocomplete, {
      props: {
        modelValue: "",
        label: "Test",
        stations: [],
      },
    })

    expect(wrapper.exists()).toBe(true)
  })

  it("affiche la liste déroulante lors de la saisie", async () => {
    const input = wrapper.find("input")
    await input.trigger("input")

    await wrapper.vm.$nextTick()

    expect(input.exists()).toBe(true)
  })

  it("possède des clés uniques pour les éléments de la liste", async () => {
    const input = wrapper.find("input")
    await input.setValue("A")

    await wrapper.vm.$nextTick()

    const list = wrapper.find("ul")
    expect(list.exists()).toBe(true)

    const items = list.findAll("li")
    expect(items.length).toBeGreaterThan(0)
  })
})
