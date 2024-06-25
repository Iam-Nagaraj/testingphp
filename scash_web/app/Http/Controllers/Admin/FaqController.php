<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class FaqController extends Controller
{
	protected Faq $faqService;

	public function __construct(Faq $faqService)
	{
		$this->faqService = $faqService;
	}

	/**
	 * Faq Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.faq.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Faq Table List
	 *
	 */
	public function table(Request $request)
	{

		$faqs = $this->faqService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$faqs->where(function ($query) use ($searchValue) {
				$query->where('title', 'LIKE', $searchValue);
			});
		}
		$faqs = $faqs->latest('id');
		return Datatables::of($faqs)->addColumn('action', function ($row) {
			return view('admin.faq.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.faq.view', ['id' => $row->id]),
					'delete_url' => route('admin.faq.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	/**
	 * Faq Create Form
	 *
	 */
	public function view(Request $request)
	{
		$stateModel = State::select('id','name')->pluck('name','id');
		$detail = $this->faqService->fetchByID($request->id);
		return view('admin.faq.view')->with(['detail' => $detail, 'stateModel' => $stateModel]);
	}

	public function create()
	{
		$stateModel = State::select('id','name')->pluck('name','id');
		try {
			return view('admin.faq.create', compact('stateModel'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Faq Save data
	 *
	 */
	public function save(FaqRequest $request)
	{

		try {
			$userModel = Faq::updateOrCreate(
				['id' => $request->id],
				[
					'title' => $request->title,
					'description' => $request->description
				]
			);

			return $this->sendResponse(true, $userModel, 'Faq successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Faq Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->faqService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("Faq not found.", 404);
			}

			$this->faqService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
