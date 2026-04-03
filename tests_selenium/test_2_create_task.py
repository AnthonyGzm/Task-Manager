import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select
import time

BASE_URL = "http://localhost/Task-Managerv2"

class TestCreateTaskFlow:
    """ US2: Creación de Tareas """

    def test_03_crear_tarea_flujo_negativo(self, driver):
        """[Prueba Negativa] Intentar crear una tarea sin título (Campo obligatorio) validando directamente el backend"""
        driver.get(f"{BASE_URL}/tasks/add_task.php")
        time.sleep(1)
        
        # Llenamos solo lo no obligatorio y dejamos título vacío
        driver.find_element(By.NAME, "description").send_keys("Intención de fallo al no tener título")
        time.sleep(1)
        
        # Debemos quitar el atributo de validación nativa de HTML5 'required' de la vista para poder someter el form al backend
        title_input = driver.find_element(By.NAME, "title")
        driver.execute_script("arguments[0].removeAttribute('required')", title_input)
        time.sleep(1)
        
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        
        # Debemos ver la alerta de error del backend
        error_msg = WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".alert.alert-danger"))
        )
        time.sleep(1)
        assert "Debe completar el título" in error_msg.text

    def test_04_crear_tarea_flujo_positivo(self, driver):
        """[Happy Path / Prueba Positiva] Crear una tarea llenando todos los campos correctamente"""
        # Refrescamos para limpiar el error previo
        driver.refresh()
        time.sleep(1)
        
        driver.find_element(By.NAME, "title").send_keys("Automatización Selenium")
        time.sleep(1)
        driver.find_element(By.NAME, "description").send_keys("Verificando funcionamiento general despacio.")
        time.sleep(1)
        
        Select(driver.find_element(By.NAME, "status")).select_by_visible_text("Pendiente")
        Select(driver.find_element(By.NAME, "priority")).select_by_visible_text("Alta")
        time.sleep(1)
        
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        
        # Validar redirección al Dashboard
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, "table"))
        )
        time.sleep(1) # pausa al volver a dashboard
        assert "Automatización Selenium" in driver.page_source
