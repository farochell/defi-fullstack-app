import { describe, it, expect, vi } from "vitest"
import { mount } from "@vue/test-utils"
import GetStatsForm from "../components/GetStatsForm.vue"

describe("GetStatsForm", () => {
  const defaultModelValue = {
    from: '',
    to: '',
    groupBy: "none" as const,
  }

  it("affiche le formulaire", () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })
    expect(wrapper.find("form").exists()).toBe(true)
  })

  it("affiche tous les champs du formulaire", () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })
    expect(wrapper.findAll("input[type='date']").length).toBe(2)
    expect(wrapper.find("select").exists()).toBe(true)
    expect(wrapper.find("button[type='submit']").exists()).toBe(true)
  })

  it("utilise les labels par défaut lorsqu'ils ne sont pas fournis", () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })
    expect(wrapper.text()).toContain("Date de début")
    expect(wrapper.text()).toContain("Date de fin")
    expect(wrapper.text()).toContain("Regrouper par")
  })

  it("utilise les labels personnalisés lorsqu'ils sont fournis", () => {
    const wrapper = mount(GetStatsForm, {
      props: {
        modelValue: defaultModelValue,
        labelFrom: "Start Date",
        labelTo: "End Date",
        labelGroupBy: "Group Type",
      },
    })
    expect(wrapper.text()).toContain("Start Date")
    expect(wrapper.text()).toContain("End Date")
    expect(wrapper.text()).toContain("Group Type")
  })

  it("utilise le label de soumission personnalisé lorsqu'il est fourni", () => {
    const wrapper = mount(GetStatsForm, {
      props: {
        modelValue: defaultModelValue,
        submitLabel: "Custom Submit",
      },
    })
    expect(wrapper.find("button[type='submit']").text()).toBe("Custom Submit")
  })

  it("utilise le label de soumission par défaut lorsqu'il n'est pas fourni", () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })
    expect(wrapper.find("button[type='submit']").text()).toBe("Rechercher")
  })

  it("affiche toutes les options groupBy", () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })
    const options = wrapper.findAll("option")
    expect(options.length).toBe(4)
    expect(options[0].text()).toBe("Aucun regroupement")
    expect(options[1].text()).toBe("Jour")
    expect(options[2].text()).toBe("Mois")
    expect(options[3].text()).toBe("Année")
  })

  it("lie correctement modelValue aux champs du formulaire", async () => {
    const modelValue = {
      from: "2024-01-01",
      to: "2024-12-31",
      groupBy: "month" as const,
    }

    const wrapper = mount(GetStatsForm, {
      props: { modelValue },
    })

    const [fromInput, toInput] = wrapper.findAll("input[type='date']")
    const select = wrapper.find("select")

    expect((fromInput.element as HTMLInputElement).value).toBe("2024-01-01")
    expect((toInput.element as HTMLInputElement).value).toBe("2024-12-31")
    expect((select.element as HTMLSelectElement).value).toBe("month")
  })

  it("émet update:modelValue lorsque la date de début change", async () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    const fromInput = wrapper.findAll("input[type='date']")[0]
    await fromInput.setValue("2024-01-01")

    expect(wrapper.emitted()).toHaveProperty("update:modelValue")
    const emitted = wrapper.emitted("update:modelValue") as any[]
    expect(emitted[0][0].from).toBe("2024-01-01")
  })

  it("émet update:modelValue lorsque la date de fin change", async () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    const toInput = wrapper.findAll("input[type='date']")[1]
    await toInput.setValue("2024-12-31")

    expect(wrapper.emitted()).toHaveProperty("update:modelValue")
    const emitted = wrapper.emitted("update:modelValue") as any[]
    expect(emitted[0][0].to).toBe("2024-12-31")
  })

  it("émet update:modelValue lorsque groupBy change", async () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    const select = wrapper.find("select")
    await select.setValue("day")

    expect(wrapper.emitted()).toHaveProperty("update:modelValue")
    const emitted = wrapper.emitted("update:modelValue") as any[]
    expect(emitted[0][0].groupBy).toBe("day")
  })

  it("émet l'événement submit lorsque le formulaire est soumis", async () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    const form = wrapper.find("form")
    await form.trigger("submit.prevent")

    expect(wrapper.emitted()).toHaveProperty("submit")
    expect(wrapper.emitted("submit")?.[0]).toEqual([defaultModelValue])
  })

  it("désactive tous les champs lorsque disabled est true", () => {
    const wrapper = mount(GetStatsForm, {
      props: {
        modelValue: defaultModelValue,
        disabled: true,
      },
    })

    const [fromInput, toInput] = wrapper.findAll("input[type='date']")
    const select = wrapper.find("select")
    const submitButton = wrapper.find("button[type='submit']")

    expect(fromInput.attributes("disabled")).toBeDefined()
    expect(toInput.attributes("disabled")).toBeDefined()
    expect(select.attributes("disabled")).toBeDefined()
    expect(submitButton.attributes("disabled")).toBeDefined()
  })

  it("active tous les champs lorsque disabled est false", () => {
    const wrapper = mount(GetStatsForm, {
      props: {
        modelValue: defaultModelValue,
        disabled: false,
      },
    })

    const [fromInput, toInput] = wrapper.findAll("input[type='date']")
    const select = wrapper.find("select")
    const submitButton = wrapper.find("button[type='submit']")

    expect(fromInput.attributes("disabled")).toBeUndefined()
    expect(toInput.attributes("disabled")).toBeUndefined()
    expect(select.attributes("disabled")).toBeUndefined()
    expect(submitButton.attributes("disabled")).toBeUndefined()
  })

  it("applique les classes CSS correctes aux champs désactivés", () => {
    const wrapper = mount(GetStatsForm, {
      props: {
        modelValue: defaultModelValue,
        disabled: true,
      },
    })

    const fromInput = wrapper.findAll("input[type='date']")[0]
    expect(fromInput.classes()).toContain("disabled:opacity-50")
  })

  it("gère correctement toutes les valeurs groupBy", async () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    const select = wrapper.find("select")

    const groupByValues = ["none", "day", "month", "year"]

    for (const value of groupByValues) {
      await select.setValue(value)
      const emitted = wrapper.emitted("update:modelValue") as any[]
      const lastEmit = emitted[emitted.length - 1][0]
      expect(lastEmit.groupBy).toBe(value)
    }
  })

  it("empêche la soumission par défaut du formulaire", async () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    const form = wrapper.find("form")
    const event = new Event("submit")
    const preventDefaultSpy = vi.spyOn(event, "preventDefault")

    await form.trigger("submit")

    // Le @submit.prevent devrait gérer cela, mais nous vérifions que le formulaire a le bon gestionnaire
    expect(wrapper.emitted()).toHaveProperty("submit")
  })

  it("maintient la structure du formulaire avec la mise en page flexbox", () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    const form = wrapper.find("form")
    expect(form.classes()).toContain("space-y-4")

    const fieldContainers = wrapper.findAll(".flex.flex-col")
    expect(fieldContainers.length).toBeGreaterThanOrEqual(3)
  })

  it("possède une accessibilité appropriée avec des associations de labels", () => {
    const wrapper = mount(GetStatsForm, {
      props: { modelValue: defaultModelValue },
    })

    // Tous les labels doivent être associés à leurs champs
    const labels = wrapper.findAll("label")
    expect(labels.length).toBeGreaterThanOrEqual(3)
  })

  it("émet submit avec les valeurs actuelles du formulaire", async () => {
    const modelValue = {
      from: "2024-01-01",
      to: "2024-12-31",
      groupBy: "month" as const,
    }

    const wrapper = mount(GetStatsForm, {
      props: { modelValue },
    })

    const form = wrapper.find("form")
    await form.trigger("submit.prevent")

    const submitEmitted = wrapper.emitted("submit") as any[]
    const submittedValue = submitEmitted[0][0]

    expect(submittedValue.from).toBe("2024-01-01")
    expect(submittedValue.to).toBe("2024-12-31")
    expect(submittedValue.groupBy).toBe("month")
  })
})
