import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select
import time

BASE_URL = "http://localhost/Task-Managerv2"

class TestUpdateTaskFlow:
    """ US4: Actualización de Tareas """

    def test_06_actualizar_tarea_flujo_negativo(self, driver):
        """[Prueba Negativa] Intentar actualizar una tarea vaciándole el título requerido"""
        driver.get(f"{BASE_URL}/dashboard.php")
        time.sleep(1)
        
        edit_btn = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, "a[title='Editar']"))
        )
        edit_btn.click()
        time.sleep(1)
        
        title_input = driver.find_element(By.NAME, "title")
        driver.execute_script("arguments[0].removeAttribute('required')", title_input)
        title_input.clear()
        time.sleep(1)
        
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        
        error_msg = WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".alert.alert-danger"))
        )
        time.sleep(1)
        assert "Debe completar el título" in error_msg.text

    def test_07_actualizar_tarea_flujo_positivo(self, driver):
        """[Happy Path / Prueba Positiva] Modificar parámetros de Título y Estado"""
        driver.refresh()
        time.sleep(1)
        
        title_input = driver.find_element(By.NAME, "title")
        title_input.clear()
        title_input.send_keys("Automatización Finalizada")
        time.sleep(1)
        
        Select(driver.find_element(By.NAME, "status")).select_by_visible_text("Completada")
        time.sleep(1)
        
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, "table"))
        )
        time.sleep(1)
        assert "Automatización Finalizada" in driver.page_source
