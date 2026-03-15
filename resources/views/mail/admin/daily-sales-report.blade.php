<x-mail::message>
# Sales Report Date: ({{ $report['report_date'] }})

Hello {{ $user->name }},

**Total Revenue:** {{ number_format((float) collect($report['revenue_by_site'])->sum('totals_count'), 2, '.', ' ') }}

## Sold Products (Quantity)

- Best seller:
@if (!empty($report['most_sold_product']))
{{ $report['most_sold_product']->name }} ({{ $report['most_sold_product']->quantities_count }})
@endif
- Least seller:
@if (!empty($report['least_sold_product']))
{{ $report['least_sold_product']->name }} ({{ $report['least_sold_product']->quantities_count }})
@endif

## Products by Revenue

- Highest revenue:
@if (!empty($report['highest_revenue_product']))
{{ $report['highest_revenue_product']->name }}
({{ number_format((float) $report['highest_revenue_product']->lines_total_count, 2, '.', ' ') }})
@endif
- Lowest revenue:
@if (!empty($report['lowest_revenue_product']))
{{ $report['lowest_revenue_product']->name }}
({{ number_format((float) $report['lowest_revenue_product']->lines_total_count, 2, '.', ' ') }})
@endif

## Revenue by Site

<x-mail::table>
| Site | Revenue |
| :--- | --: |
@foreach ($report['revenue_by_site'] as $siteRevenue)
| {{ $siteRevenue->name }} ({{ $siteRevenue->code }}) | {{ number_format((float) $siteRevenue->totals_count, 2, '.', ' ') }} |
@endforeach
</x-mail::table>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
