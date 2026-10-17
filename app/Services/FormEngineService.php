<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class FormEngineService
{
    /**
     * Generate Laravel validation rules based on a JSON-driven form configuration.
     *
     * @param array $fields List of field definitions
     * @return array
     */
    public function getValidationRules(array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $name = $field['name'] ?? null;
            if (!$name) continue;

            $fieldRules = [];

            if (!empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field['type'] ?? 'text') {
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'gps':
                case 'location':
                    $fieldRules[] = 'array';
                    break;
                case 'signature':
                case 'camera':
                case 'upload':
                    // In dynamic forms, these can either be files or base64 signature strings or links
                    $fieldRules[] = 'string';
                    break;
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$name] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Render HTML input elements for custom form field definitions.
     *
     * @param array $fields
     * @param array|null $values
     * @return string
     */
    public function renderHtml(array $fields, ?array $values = null): string
    {
        $html = '';
        foreach ($fields as $field) {
            $name = $field['name'] ?? '';
            $label = $field['label'] ?? '';
            $type = $field['type'] ?? 'text';
            $required = !empty($field['required']) ? 'required' : '';
            $value = e($values[$name] ?? $field['default'] ?? '');

            $html .= "<div class='mb-5'>";
            $html .= "<label class='block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2'>{$label}" . ($required ? " <span class='text-red-500'>*</span>" : "") . "</label>";

            switch ($type) {
                case 'textarea':
                    $html .= "<textarea name='content_data[{$name}]' class='w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50] bg-white text-sm transition' rows='3' {$required}>{$value}</textarea>";
                    break;

                case 'select':
                    $html .= "<select name='content_data[{$name}]' class='w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50] bg-white text-sm transition' {$required}>";
                    $html .= "<option value=''>-- Select Option --</option>";
                    foreach ($field['options'] ?? [] as $option) {
                        $selected = ($value == $option) ? 'selected' : '';
                        $html .= "<option value='{$option}' {$selected}>{$option}</option>";
                    }
                    $html .= "</select>";
                    break;

                case 'radio':
                    $html .= "<div class='flex flex-wrap gap-4 mt-2'>";
                    foreach ($field['options'] ?? [] as $option) {
                        $checked = ($value == $option) ? 'checked' : '';
                        $html .= "<label class='inline-flex items-center text-sm text-gray-700'>";
                        $html .= "<input type='radio' name='content_data[{$name}]' value='{$option}' class='text-[#4CAF50] focus:ring-[#4CAF50] border-gray-300' {$checked} {$required}>";
                        $html .= "<span class='ml-2'>{$option}</span>";
                        $html .= "</label>";
                    }
                    $html .= "</div>";
                    break;

                case 'checkbox':
                    // Checkbox handles array value
                    $html .= "<div class='flex flex-wrap gap-4 mt-2'>";
                    foreach ($field['options'] ?? [] as $option) {
                        $checked = (is_array($values[$name] ?? null) && in_array($option, $values[$name])) ? 'checked' : '';
                        $html .= "<label class='inline-flex items-center text-sm text-gray-700'>";
                        $html .= "<input type='checkbox' name='content_data[{$name}][]' value='{$option}' class='rounded text-[#4CAF50] focus:ring-[#4CAF50] border-gray-300' {$checked}>";
                        $html .= "<span class='ml-2'>{$option}</span>";
                        $html .= "</label>";
                    }
                    $html .= "</div>";
                    break;

                case 'signature':
                    // Interactive Canvas Signature Field
                    $html .= "<div x-data='signaturePad()' class='border border-dashed border-gray-300 rounded-lg p-4 bg-gray-50'>";
                    $html .= "  <canvas x-ref='canvas' class='border border-gray-200 rounded bg-white w-full h-40 touch-none'></canvas>";
                    $html .= "  <input type='hidden' name='content_data[{$name}]' x-model='signature'>";
                    $html .= "  <div class='flex justify-between items-center mt-3'>";
                    $html .= "    <button type='button' @click='clear()' class='text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-full transition'>Clear Signature</button>";
                    $html .= "    <span class='text-xs text-gray-400'>Draw on the pad above</span>";
                    $html .= "  </div>";
                    $html .= "</div>";
                    break;

                case 'gps':
                case 'location':
                    // GPS capture field using browser API
                    $html .= "<div x-data='gpsTracker()' class='flex items-center gap-3 bg-gray-50 border border-gray-200 p-3 rounded-lg'>";
                    $html .= "  <input type='hidden' name='content_data[{$name}][lat]' x-model='lat'>";
                    $html .= "  <input type='hidden' name='content_data[{$name}][lng]' x-model='lng'>";
                    $html .= "  <button type='button' @click='getLocation()' class='bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2 rounded-full shadow transition flex items-center gap-1'>";
                    $html .= "     <svg class='w-3 h-3' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'/><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg>";
                    $html .= "     Get Coordinates";
                    $html .= "  </button>";
                    $html .= "  <span x-text='status' class='text-xs text-gray-500 font-medium'>GPS coordinates not captured yet</span>";
                    $html .= "</div>";
                    break;

                case 'camera':
                case 'upload':
                    // File upload or camera photo take
                    $html .= "<div class='flex flex-col gap-2'>";
                    $html .= "  <input type='file' name='file_{$name}' accept='image/*' class='block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-lime-50 file:text-lime-700 hover:file:bg-lime-100'>";
                    $html .= "  <span class='text-[10px] text-gray-400'>Select a file or take a picture</span>";
                    $html .= "</div>";
                    break;

                default:
                    $html .= "<input type='{$type}' name='content_data[{$name}]' value='{$value}' class='w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50] bg-white text-sm transition' {$required}>";
                    break;
            }

            $html .= "</div>";
        }

        return $html;
    }
}
