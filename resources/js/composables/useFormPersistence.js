import { ref, watch, onMounted } from 'vue'

export function useFormPersistence(storageKey, formData, options = {}) {
    const {
        excludeFields = [],
        autoSave = true,
        showNotification = true
    } = options

    const showDraftNotification = ref(false)

    // Load saved form data from localStorage
    const loadSavedFormData = () => {
        try {
            const saved = localStorage.getItem(storageKey)
            if (saved) {
                const parsedData = JSON.parse(saved)
                
                // Convert date strings back to Date objects
                Object.keys(parsedData).forEach(key => {
                    if (parsedData[key] && typeof parsedData[key] === 'string') {
                        // Check if it's a date string (ISO format)
                        const dateRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/
                        if (dateRegex.test(parsedData[key])) {
                            parsedData[key] = new Date(parsedData[key])
                        }
                    }
                })
                
                return parsedData
            }
        } catch (error) {
            console.error('Error loading saved form data:', error)
        }
        return null
    }

    // Save form data to localStorage
    const saveFormData = () => {
        try {
            const dataToSave = {}
            
            // Only save fields that are not excluded
            Object.keys(formData).forEach(key => {
                if (!excludeFields.includes(key)) {
                    dataToSave[key] = formData[key]
                }
            })
            
            localStorage.setItem(storageKey, JSON.stringify(dataToSave))
        } catch (error) {
            console.error('Error saving form data:', error)
        }
    }

    // Clear saved form data
    const clearSavedFormData = () => {
        try {
            localStorage.removeItem(storageKey)
            showDraftNotification.value = false
        } catch (error) {
            console.error('Error clearing saved form data:', error)
        }
    }

    // Initialize form with saved data
    const initializeForm = () => {
        const savedData = loadSavedFormData()
        
        if (savedData) {
            // Restore saved data to form
            Object.keys(savedData).forEach(key => {
                if (formData.hasOwnProperty(key)) {
                    formData[key] = savedData[key]
                }
            })
            
            if (showNotification) {
                showDraftNotification.value = true
            }
        }
        
        return !!savedData
    }

    // Set up auto-save watchers
    const setupAutoSave = () => {
        if (!autoSave) return

        Object.keys(formData).forEach(key => {
            if (!excludeFields.includes(key)) {
                watch(() => formData[key], saveFormData, { deep: true })
            }
        })
    }

    // Handle form submission success
    const onSubmitSuccess = () => {
        clearSavedFormData()
    }

    // Handle form cancellation
    const onCancel = (hasChanges = false) => {
        if (hasChanges) {
            const keepDraft = confirm('You have unsaved changes. Do you want to keep your draft for later?')
            if (!keepDraft) {
                clearSavedFormData()
            }
        } else {
            clearSavedFormData()
        }
    }

    // Check if form has unsaved changes
    const hasUnsavedChanges = () => {
        const saved = loadSavedFormData()
        return !!saved
    }

    return {
        showDraftNotification,
        initializeForm,
        setupAutoSave,
        saveFormData,
        clearSavedFormData,
        onSubmitSuccess,
        onCancel,
        hasUnsavedChanges
    }
}
